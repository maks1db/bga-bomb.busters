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

class SetupInfo extends GameState
{
    public function __construct(protected Game $game)
    {
        parent::__construct($game, id: 10, type: StateType::MULTIPLE_ACTIVE_PLAYER);
    }

    public function onEnteringState(): ?string
    {
        $playerIds = $this->game->getPlayersWithBlueWires();
        if ($playerIds === []) {
            return StartGame::class;
        }
        $this->gamestate->setPlayersMultiactive($playerIds, StartGame::class, true);
        return null;
    }

    #[PossibleAction]
    public function actChooseInitialInfo(int $wireId, int $currentPlayerId): string
    {
        $wire = $this->game->wires->getItemById($wireId);
        if (
            $wire === null
            || $wire->location !== 'hand'
            || (int)$wire->locationArg !== $currentPlayerId
            || $wire->color !== 'blue'
        ) {
            throw new UserException(clienttranslate('Выберите синий провод из своей руки'));
        }

        $wire->infoRevealed = true;
        $this->game->wires->updateItem($wire, 'infoRevealed');
        $this->notify->all('initialInfo', clienttranslate('${player_name} размещает начальный информационный жетон'), [
            'player_id' => $currentPlayerId,
            'player_name' => $this->game->getPlayerNameById($currentPlayerId),
            'wire' => $this->game->getWireData($wire, false),
        ]);

        return StartGame::class;
    }

    public function zombie(int $playerId): string
    {
        $choices = array_values(array_filter(
            $this->game->wires->getItemsInLocation(['hand', $playerId])->values(),
            static fn(Wire $wire): bool => $wire->color === 'blue',
        ));
        if ($choices === []) {
            throw new SystemException('У игрока нет синего провода для начального информационного жетона');
        }
        $wire = $this->getRandomZombieChoice($choices);
        return $this->actChooseInitialInfo($wire->id, $playerId);
    }
}
