<?php

declare(strict_types=1);

namespace Bga\Games\bombbusters\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\SystemException;
use Bga\GameFramework\UserException;
use Bga\Games\bombbusters\Game;
use Bga\Games\bombbusters\Wire;

class PlayerTurn extends GameState
{
    public function __construct(protected Game $game)
    {
        parent::__construct($game, id: 20, type: StateType::ACTIVE_PLAYER, updateGameProgression: true);
    }

    public function getArgs(int $activePlayerId): array
    {
        return $this->game->getTurnArgs($activePlayerId);
    }

    #[PossibleAction]
    public function actDualCut(int $targetWireId, string $value, int $activePlayerId, array $args): string
    {
        if (!in_array($value, $args['dualValues'], true)) {
            throw new UserException(clienttranslate('Это значение нельзя выбрать для двойного разреза'));
        }
        $target = $this->game->wires->getItemById($targetWireId);
        if ($target === null || $target->location !== 'hand' || (int)$target->locationArg === $activePlayerId) {
            throw new UserException(clienttranslate('Выберите провод другого игрока'));
        }
        $ownWires = $this->game->getOwnWiresByValue($activePlayerId, $value);
        if ($ownWires === []) {
            throw new UserException(clienttranslate('В вашей руке нет провода с этим значением'));
        }
        $own = $ownWires[0];
        $targetOwnerId = (int)$target->locationArg;

        if ($target->getValue() === $value && $value !== 'red') {
            $this->game->wires->moveItem($target, ['cut', $targetOwnerId]);
            $this->game->wires->moveItem($own, ['cut', $activePlayerId]);
            $validatedValues = $this->game->updateValidatedValues();
            $message = $value === 'yellow'
                ? clienttranslate('${player_name} успешно перерезает два жёлтых провода')
                : clienttranslate('${player_name} успешно перерезает два провода со значением ${value}');
            $this->notify->all('dualCutSuccess', $message, [
                'player_id' => $activePlayerId,
                'player_name' => $this->game->getPlayerNameById($activePlayerId),
                'value' => $value,
                'wires' => [
                    $this->game->getWireData($own, true),
                    $this->game->getWireData($target, true),
                ],
                'validatedValues' => $validatedValues,
            ]);
            return $this->afterRemovedWires();
        }

        if ($target->color === 'red') {
            $this->game->wires->moveItem($target, ['cut', $targetOwnerId]);
            $this->game->setOutcome('red');
            $this->notify->all('redWireExploded', clienttranslate('${player_name} перерезает красный провод. Бомба взрывается!'), [
                'player_id' => $activePlayerId,
                'player_name' => $this->game->getPlayerNameById($activePlayerId),
                'wire' => $this->game->getWireData($target, true),
            ]);
            return EndScore::class;
        }

        $detonator = $this->game->advanceDetonator();
        if ($detonator >= Game::DETONATOR_MAX) {
            $this->game->setOutcome('detonator');
            $this->notify->all('detonatorExploded', clienttranslate('Детонатор достигает черепа. Бомба взрывается!'), [
                'detonator' => $detonator,
            ]);
            return EndScore::class;
        }

        $target->infoRevealed = true;
        $this->game->wires->updateItem($target, 'infoRevealed');
        $this->notify->all('dualCutFailure', clienttranslate('Значение не совпало. Детонатор продвигается, а провод получает информационный жетон'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'wire' => $this->game->getWireData($target, false),
            'detonator' => $detonator,
        ]);
        return NextPlayer::class;
    }

    #[PossibleAction]
    public function actSoloCut(string $value, int $activePlayerId, array $args): string
    {
        if (!in_array($value, $args['soloValues'], true)) {
            throw new UserException(clienttranslate('Эти провода нельзя перерезать одиночным действием'));
        }
        $wires = $this->game->getOwnWiresByValue($activePlayerId, $value);
        $this->game->wires->moveItems($wires, ['cut', $activePlayerId]);
        $validatedValues = $this->game->updateValidatedValues();
        $message = $value === 'yellow'
            ? clienttranslate('${player_name} перерезает оставшиеся жёлтые провода')
            : clienttranslate('${player_name} перерезает оставшиеся провода со значением ${value}');
        $this->notify->all('soloCut', $message, [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'value' => $value,
            'wires' => array_map(
                fn(Wire $wire): array => $this->game->getWireData($wire, true),
                $wires,
            ),
            'validatedValues' => $validatedValues,
        ]);
        return $this->afterRemovedWires();
    }

    #[PossibleAction]
    public function actRevealRed(int $activePlayerId, array $args): string
    {
        if (!$args['revealRed']) {
            throw new UserException(clienttranslate('Красные провода можно вскрыть, только когда в руке не осталось других проводов'));
        }
        $wires = $this->game->wires->getItemsInLocation(['hand', $activePlayerId])->values();
        $this->game->wires->moveItems($wires, ['revealed', $activePlayerId]);
        $this->notify->all('redWiresRevealed', clienttranslate('${player_name} безопасно вскрывает свои красные провода'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'wires' => array_map(
                fn(Wire $wire): array => $this->game->getWireData($wire, true),
                $wires,
            ),
        ]);
        return $this->afterRemovedWires();
    }

    public function zombie(int $playerId): string
    {
        $args = $this->getArgs($playerId);
        if ($args['revealRed']) {
            return $this->actRevealRed($playerId, $args);
        }
        if ($args['soloValues'] !== []) {
            $value = $this->getRandomZombieChoice($args['soloValues']);
            return $this->actSoloCut($value, $playerId, $args);
        }

        $candidates = [];
        foreach ($this->game->wires->getItemsInLocation('hand') as $wire) {
            if (
                (int)$wire->locationArg !== $playerId
                && $wire->color !== 'red'
                && in_array($wire->getValue(), $args['dualValues'], true)
            ) {
                $candidates[] = ['targetWireId' => $wire->id, 'value' => $wire->getValue()];
            }
        }
        if ($candidates === []) {
            throw new SystemException('Не найдено безопасное действие для зомби-игрока');
        }
        $choice = $this->getRandomZombieChoice($candidates);
        return $this->actDualCut($choice['targetWireId'], $choice['value'], $playerId, $args);
    }

    private function afterRemovedWires(): string
    {
        if ($this->game->allHandsEmpty()) {
            $this->game->setOutcome('success');
            return EndScore::class;
        }
        return NextPlayer::class;
    }
}
