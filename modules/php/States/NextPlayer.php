<?php

declare(strict_types=1);

namespace Bga\Games\bombbusters\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\Games\bombbusters\Game;

class NextPlayer extends GameState
{
    public function __construct(protected Game $game)
    {
        parent::__construct($game, id: 90, type: StateType::GAME, updateGameProgression: true);
    }

    public function onEnteringState(): string
    {
        $playerCount = $this->game->getPlayersNumber();
        for ($index = 0; $index < $playerCount; $index++) {
            $playerId = (int)$this->game->activeNextPlayer();
            if ($this->game->hasWires($playerId)) {
                $this->game->giveExtraTime($playerId);
                return PlayerTurn::class;
            }
        }

        $this->game->setOutcome('success');
        return EndScore::class;
    }
}
