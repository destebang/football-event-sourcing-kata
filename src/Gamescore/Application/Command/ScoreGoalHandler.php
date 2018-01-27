<?php

namespace Football\Gamescore\Application\Command;

use Football\Gamescore\Domain\Aggregate\GameList;

class ScoreGoalHandler
{
    /**
     * @var GameList
     */
    private $gameList;

    public function __construct(GameList $todoList)
    {
        $this->gameList = $todoList;
    }

    public function __invoke(ScoreGoal $command): void
    {
        $game = $this->gameList->get($command->gameId());
        $game->scoreGoal($command->playerId());
        $this->gameList->save($game);
    }
}
