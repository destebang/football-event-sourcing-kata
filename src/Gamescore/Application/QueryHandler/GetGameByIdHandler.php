<?php

namespace Football\Gamescore\Application\QueryHandler;


use Football\Gamescore\Domain\Query\GetGameById;
use Football\Gamescore\Projection\Game\GameFinder;
use React\Promise\Deferred;

class GetGameByIdHandler
{
    /**
     * @var GameFinder
     */
    private $gameFinder;

    public function __construct(GameFinder $gameFinder)
    {
        $this->gameFinder = $gameFinder;
    }

    public function __invoke(GetGameById $query, Deferred $deferred)
    {
        $game = $this->gameFinder->findById($query->gameId());

        if (null === $deferred) {
            return $game;
        }

        $deferred->resolve($game);
    }
}