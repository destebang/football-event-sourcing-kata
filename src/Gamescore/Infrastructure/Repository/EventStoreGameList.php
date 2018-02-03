<?php

namespace Football\Gamescore\Infrastructure\Repository;

use Football\Gamescore\Domain\Aggregate\Game;
use Football\Gamescore\Domain\Aggregate\GameId;
use Football\Gamescore\Domain\Aggregate\GameList;
use Prooph\EventSourcing\Aggregate\AggregateRepository;

class EventStoreGameList extends AggregateRepository implements GameList
{
    public function get(GameId $gameId): Game
    {
        return $this->getAggregateRoot($gameId->toString());
    }

    public function save(Game $game): void
    {
        $this->saveAggregateRoot($game);
    }
}