<?php

declare(strict_types=1);

namespace Bga\Games\bombbusters\States;

use Bga\GameFramework\GameResult\GameResult;
use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\Games\bombbusters\Game;

class EndScore extends GameState
{
    public function __construct(protected Game $game)
    {
        parent::__construct($game, id: 98, type: StateType::GAME);
    }

    public function onEnteringState(): GameResult
    {
        $outcome = (string)$this->globals->get('outcome', 'detonator');
        $win = $outcome === 'success';
        $message = $win
            ? clienttranslate('Все провода убраны с подставок. Миссия выполнена!')
            : clienttranslate('Миссия завершилась провалом.');
        $this->notify->all('missionEnded', $message, ['outcome' => $outcome]);

        return GameResult::coop(score: $win ? 1 : 0, win: $win);
    }
}
