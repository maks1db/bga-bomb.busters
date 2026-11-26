<?php

declare(strict_types=1);

namespace Bga\Games\bombbusters\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\Games\bombbusters\Game;

class StartGame extends GameState
{
    public function __construct(protected Game $game)
    {
        parent::__construct($game, id: 15, type: StateType::GAME);
    }

    public function onEnteringState(): string
    {
        $captainId = (int)$this->globals->get('captain_id');
        $this->gamestate->changeActivePlayer($captainId);
        return PlayerTurn::class;
    }
}
