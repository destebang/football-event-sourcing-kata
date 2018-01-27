<?php

namespace Football\Gamescore\Application\Command;

use Football\Gamescore\Domain\Aggregate\Game;
use Football\Gamescore\Domain\Aggregate\GameList;
use Football\Gamescore\Domain\Service\Game\LineupPolicy;

class StartGameHandler
{
    /**
     * @var LineupPolicy
     */
    private $lineupPolicy;

    /**
     * @var GameList
     */
    private $gameList;

    public function __construct(LineupPolicy $lineupPolicy, GameList $todoList)
    {
        $this->lineupPolicy = $lineupPolicy;
        $this->gameList = $todoList;
    }

    public function __invoke(StartGame $command): void
    {
        $game = Game::startGame(
            $command->gameId(),
            $command->localTeam(),
            $command->visitorTeam(),
            $this->lineupPolicy
        );

        $this->gameList->save($game);
    }
}