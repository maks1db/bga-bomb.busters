<?php

declare(strict_types=1);

namespace Bga\Games\bombbusters;

use Bga\GameFramework\Components\ItemManager\ItemLocation;
use Bga\GameFramework\Components\ItemManager\ItemManager;
use Bga\Games\bombbusters\States\SetupInfo;

class Game extends \Bga\GameFramework\Table
{
    public const DETONATOR_MAX = 6;

    public ItemManager $wires;

    public function __construct()
    {
        parent::__construct();

        $this->wires = $this->bga->itemManagerFactory->createItemManager(
            Wire::class,
            locations: [
                new ItemLocation('deck', randomPick: true),
                new ItemLocation('hand'),
                new ItemLocation('cut'),
                new ItemLocation('revealed'),
            ],
        );
    }

    public function getGameProgression(): int
    {
        $removed = $this->wires->countItemsInLocation('cut') + $this->wires->countItemsInLocation('revealed');
        $total = Missions::getWireCount($this->getMission());
        return min(100, (int)round($removed * 100 / $total));
    }

    public function getMission(): array
    {
        $missionId = (int)$this->bga->globals->get('mission_id', 1);
        return Missions::get($missionId);
    }

    public function getDetonatorMax(): int
    {
        return self::DETONATOR_MAX;
    }

    public function upgradeTableDb($from_version)
    {
    }

    public function getAllDatas(int $currentPlayerId): array
    {
        $result = [];
        $result['players'] = $this->getCollectionFromDb(
            'SELECT `player_id` AS `id`, `player_score` AS `score` FROM `player`'
        );

        $playerCount = $this->getPlayersNumber();
        $captainId = (int)$this->bga->globals->get('captain_id');
        foreach (array_keys($result['players']) as $playerId) {
            $standCount = 1;
            if ($playerCount === 2 || ($playerCount === 3 && (int)$playerId === $captainId)) {
                $standCount = 2;
            }
            $result['players'][$playerId]['standCount'] = $standCount;
            $result['hands'][(int)$playerId] = [];
        }

        foreach ($this->wires->getItemsInLocation('hand') as $wire) {
            $ownerId = (int)$wire->locationArg;
            $result['hands'][$ownerId][] = $this->getWireData($wire, $ownerId === $currentPlayerId);
        }

        $removedWires = array_merge(
            $this->wires->getItemsInLocation('cut')->values(),
            $this->wires->getItemsInLocation('revealed')->values(),
        );
        $result['cutWires'] = array_map(
            fn(Wire $wire): array => $this->getWireData($wire, true),
            $removedWires,
        );
        $result['specialWires'] = $this->getSpecialWireMarkers();
        $result['validatedValues'] = $this->getValidatedValues();
        $result['mission'] = $this->getMission();
        $result['detonator'] = $this->getDetonator();
        $result['detonatorMax'] = $this->getDetonatorMax();
        $result['captainId'] = $captainId;
        $result['viewerId'] = $currentPlayerId;
        $result['outcome'] = $this->bga->globals->get('outcome', null);

        return $result;
    }

    protected function setupNewGame($players, $options = [])
    {
        $gameinfos = $this->getGameinfos();
        $colors = $gameinfos['player_colors'];
        $values = [];
        foreach ($players as $playerId => $player) {
            $values[] = vsprintf("(%s, '%s', '%s')", [
                $playerId,
                array_shift($colors),
                addslashes($player['player_name']),
            ]);
        }
        $this->DbQuery('INSERT INTO `player` (`player_id`, `player_color`, `player_name`) VALUES ' . implode(',', $values));
        $this->reattributeColorsBasedOnPreferences($players, $gameinfos['player_colors']);
        $this->reloadPlayersBasicInfos();

        $captainId = (int)$this->activeNextPlayer();
        $missionId = $this->bga->tableOptions->get(Missions::OPTION_ID) ?? 1;
        $mission = Missions::get($missionId);
        $this->bga->globals->set('mission_id', $missionId);
        $this->bga->globals->set('captain_id', $captainId);
        $this->bga->globals->set('detonator', 0);
        $this->bga->globals->set('outcome', null);
        $this->bga->globals->set('validated_values', []);

        $this->wires->initDb();
        $rows = [];
        foreach ($mission['blueValues'] as $value) {
            for ($copy = 0; $copy < 4; $copy++) {
                $rows[] = ['location' => 'deck', 'color' => 'blue', 'sortValue' => $value];
            }
        }

        $specialMarkers = [];
        foreach (['red', 'yellow'] as $color) {
            $count = $mission[$color]['count'];
            $candidateCount = $mission[$color]['candidates'];
            $minValue = $mission[$color]['minValue'] ?? 1;
            $maxValue = $mission[$color]['maxValue'] ?? 11;
            $pool = [];
            for ($value = $minValue; $value <= $maxValue; $value++) {
                $pool[] = $value;
            }
            $candidates = [];
            for ($index = 0; $index < $candidateCount; $index++) {
                $poolIndex = bga_rand(0, count($pool) - 1);
                $candidates[] = $pool[$poolIndex] + ($color === 'red' ? 0.5 : 0.1);
                array_splice($pool, $poolIndex, 1);
            }

            $selected = $candidates;
            while (count($selected) > $count) {
                $index = bga_rand(0, count($selected) - 1);
                array_splice($selected, $index, 1);
            }
            foreach ($selected as $sortValue) {
                $rows[] = ['location' => 'deck', 'color' => $color, 'sortValue' => $sortValue];
            }
            foreach ($candidates as $sortValue) {
                $specialMarkers[] = [
                    'color' => $color,
                    'sortValue' => $sortValue,
                    'possible' => $candidateCount > $count,
                ];
            }
        }
        usort($specialMarkers, static fn(array $a, array $b): int => [$a['color'], $a['sortValue']] <=> [$b['color'], $b['sortValue']]);
        $this->bga->globals->set('special_wire_markers', $specialMarkers);
        $this->wires->createItems($rows);
        $this->wires->shuffle('deck');

        $standsByPlayer = [];
        foreach (array_keys($players) as $playerId) {
            $standsByPlayer[(int)$playerId] = count($players) === 2 || (count($players) === 3 && (int)$playerId === $captainId) ? 2 : 1;
        }
        $standTotal = array_sum($standsByPlayer);
        $totalWires = Missions::getWireCount($mission);
        $baseCount = intdiv($totalWires, $standTotal);
        $extraCount = $totalWires % $standTotal;
        $standNumber = 0;

        foreach ($standsByPlayer as $playerId => $standCount) {
            for ($stand = 1; $stand <= $standCount; $stand++) {
                $count = $baseCount + ($standNumber < $extraCount ? 1 : 0);
                $standNumber++;
                $picked = $this->wires->pickItems($count, 'deck', ['hand', $playerId]);
                $sorted = $picked->sort(
                    static fn(Wire $a, Wire $b): int => $a->sortValue <=> $b->sortValue,
                )->values();
                foreach ($sorted as $position => $wire) {
                    $wire->stand = $stand;
                    $wire->position = $position;
                }
                $this->wires->updateItems($picked, ['stand', 'position']);
            }
        }

        return SetupInfo::class;
    }

    public function getWireData(Wire $wire, bool $reveal): array
    {
        $data = [
            'id' => $wire->id,
            'ownerId' => (int)$wire->locationArg,
            'stand' => $wire->stand,
            'position' => $wire->position,
            'info' => null,
        ];
        if ($wire->infoRevealed) {
            $data['info'] = ['color' => $wire->color, 'value' => $wire->getValue()];
        }
        if ($reveal) {
            $data['color'] = $wire->color;
            $data['value'] = $wire->getValue();
            $data['sortValue'] = $wire->sortValue;
        }
        return $data;
    }

    public function getPlayersWithBlueWires(): array
    {
        $playerIds = array_map('intval', array_keys($this->loadPlayersBasicInfos()));
        $playersWithBlue = [];
        foreach ($this->wires->getItemsInLocation('hand') as $wire) {
            if ($wire->color === 'blue') {
                $playersWithBlue[(int)$wire->locationArg] = true;
            }
        }
        return array_values(array_filter(
            $playerIds,
            static fn(int $playerId): bool => isset($playersWithBlue[$playerId]),
        ));
    }

    public function getTurnArgs(int $playerId): array
    {
        $ownCounts = [];
        $remainingCounts = [];
        $opponentHasWires = false;
        foreach ($this->wires->getItemsInLocation('hand') as $wire) {
            $value = $wire->getValue();
            $remainingCounts[$value] = ($remainingCounts[$value] ?? 0) + 1;
            if ((int)$wire->locationArg === $playerId) {
                $ownCounts[$value] = ($ownCounts[$value] ?? 0) + 1;
            } else {
                $opponentHasWires = true;
            }
        }

        $dualValues = [];
        $soloValues = [];
        foreach ($ownCounts as $value => $count) {
            $value = (string)$value;
            if ($value === 'red') {
                continue;
            }
            if ($opponentHasWires) {
                $dualValues[] = $value;
            }
            if ($count === $remainingCounts[$value] && ($count === 2 || $count === 4)) {
                $soloValues[] = $value;
            }
        }
        $this->sortWireValues($dualValues);
        $this->sortWireValues($soloValues);

        return [
            'dualValues' => $dualValues,
            'soloValues' => $soloValues,
            'revealRed' => $ownCounts !== [] && array_keys($ownCounts) === ['red'],
        ];
    }

    public function getOwnWiresByValue(int $playerId, string $value): array
    {
        return array_values(array_filter(
            $this->wires->getItemsInLocation(['hand', $playerId])->values(),
            static fn(Wire $wire): bool => $wire->getValue() === $value,
        ));
    }

    public function hasWires(int $playerId): bool
    {
        return $this->wires->countItemsInLocation(['hand', $playerId]) > 0;
    }

    public function allHandsEmpty(): bool
    {
        return $this->wires->countItemsInLocation('hand') === 0;
    }

    public function advanceDetonator(): int
    {
        $value = $this->getDetonator() + 1;
        $this->bga->globals->set('detonator', $value);
        return $value;
    }

    public function getDetonator(): int
    {
        return (int)$this->bga->globals->get('detonator', 0);
    }

    public function setOutcome(string $outcome): void
    {
        $this->bga->globals->set('outcome', $outcome);
    }

    public function getValidatedValues(): array
    {
        return array_map('intval', $this->bga->globals->get('validated_values', []));
    }

    public function updateValidatedValues(): array
    {
        $counts = [];
        foreach ($this->wires->getItemsInLocation('cut') as $wire) {
            if ($wire->color === 'blue') {
                $value = (int)$wire->sortValue;
                $counts[$value] = ($counts[$value] ?? 0) + 1;
            }
        }
        $validated = array_keys(array_filter($counts, static fn(int $count): bool => $count === 4));
        sort($validated);
        $this->bga->globals->set('validated_values', $validated);
        return $validated;
    }

    private function getSpecialWireMarkers(): array
    {
        return $this->bga->globals->get('special_wire_markers', []);
    }

    private function sortWireValues(array &$values): void
    {
        usort($values, static function (string $a, string $b): int {
            if ($a === $b) {
                return 0;
            }
            if ($a === 'yellow') {
                return 1;
            }
            if ($b === 'yellow') {
                return -1;
            }
            return (int)$a <=> (int)$b;
        });
    }
}
