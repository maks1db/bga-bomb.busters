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
            throw new UserException(clienttranslate('This value cannot be used for a Dual Cut'));
        }
        $target = $this->game->wires->getItemById($targetWireId);
        if ($target === null || $target->location !== 'hand' || (int)$target->locationArg === $activePlayerId) {
            throw new UserException(clienttranslate('Choose a wire belonging to another player'));
        }
        $ownWires = $this->game->getOwnWiresByValue($activePlayerId, $value);
        if ($ownWires === []) {
            throw new UserException(clienttranslate('You do not have a wire with this value'));
        }
        $own = $ownWires[0];
        $targetOwnerId = (int)$target->locationArg;

        if ($target->getValue() === $value && $value !== 'red') {
            $this->game->wires->moveItem($target, ['cut', $targetOwnerId]);
            $this->game->wires->moveItem($own, ['cut', $activePlayerId]);
            $validatedValues = $this->game->updateValidatedValues();
            $message = $value === 'yellow'
                ? clienttranslate('${player_name} successfully cuts two yellow wires')
                : clienttranslate('${player_name} successfully cuts two wires with value ${value}');
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
            $this->notify->all('redWireExploded', clienttranslate('${player_name} cuts a red wire. The bomb explodes!'), [
                'player_id' => $activePlayerId,
                'player_name' => $this->game->getPlayerNameById($activePlayerId),
                'wire' => $this->game->getWireData($target, true),
            ]);
            return EndScore::class;
        }

        $detonator = $this->game->advanceDetonator();
        if ($detonator >= Game::DETONATOR_MAX) {
            $this->game->setOutcome('detonator');
            $this->notify->all('detonatorExploded', clienttranslate('The detonator reaches the skull. The bomb explodes!'), [
                'detonator' => $detonator,
            ]);
            return EndScore::class;
        }

        $target->infoRevealed = true;
        $this->game->wires->updateItem($target, 'infoRevealed');
        $this->notify->all('dualCutFailure', clienttranslate('The value does not match. The detonator advances and the wire receives an Info token'), [
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
            throw new UserException(clienttranslate('These wires cannot be cut with a Solo Cut'));
        }
        $wires = $this->game->getOwnWiresByValue($activePlayerId, $value);
        $this->game->wires->moveItems($wires, ['cut', $activePlayerId]);
        $validatedValues = $this->game->updateValidatedValues();
        $message = $value === 'yellow'
            ? clienttranslate('${player_name} cuts the remaining yellow wires')
            : clienttranslate('${player_name} cuts the remaining wires with value ${value}');
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
            throw new UserException(clienttranslate('You can reveal red wires only when no other wires remain in your hand'));
        }
        $wires = $this->game->wires->getItemsInLocation(['hand', $activePlayerId])->values();
        $this->game->wires->moveItems($wires, ['revealed', $activePlayerId]);
        $this->notify->all('redWiresRevealed', clienttranslate('${player_name} safely reveals their red wires'), [
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
            throw new SystemException('No safe action found for the zombie player');
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
