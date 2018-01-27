<?php

namespace Football\Gamescore\Domain\Event;

use Football\Gamescore\Domain\Aggregate\GameId;
use Football\Gamescore\Domain\Aggregate\Player;
use Football\Gamescore\Domain\Aggregate\Team;
use Football\Gamescore\Domain\Aggregate\TeamId;
use Prooph\EventSourcing\AggregateChanged;

class GameStarted extends AggregateChanged
{
    /**
     * @var GameId
     */
    private $gameId;

    /**
     * @var Team
     */
    private $local;

    /**
     * @var Team
     */
    private $visitor;


    public static function withTeams(GameId $gameId, Team $local, Team $visitor): GameStarted
    {
        /** @var self $event */
        $event = self::occur($gameId->toString(), [
            'game_id' => $gameId->toString(),
            'local' => $local->toArray(),
            'visitor' => $visitor->toArray()
        ]);

        $event->gameId = $gameId;
        $event->local = $local;
        $event->visitor = $visitor;

        return $event;
    }

    public function todoId(): GameId
    {
        if (!$this->gameId) {
            $this->gameId = GameId::fromString($this->payload['game_id']);
        }

        return $this->gameId;
    }

    public function local(): Team
    {
        if (!$this->local) {
            $this->local = new Team(
                TeamId::fromString($this->payload['local']['id']),
                $this->payload['local']['name'],
                array_map($this->playerFromArray(), $this->payload['local']['on_game_players']),
                array_map($this->playerFromArray(), $this->payload['local']['bench_players'])
            );
        }

        return $this->local;
    }

    public function visitor(): Team
    {
        if (!$this->visitor) {
            $this->visitor = new Team(
                TeamId::fromString($this->payload['visitor']['id']),
                $this->payload['visitor']['name'],
                array_map($this->playerFromArray(), $this->payload['visitor']['on_game_players']),
                array_map($this->playerFromArray(), $this->payload['visitor']['bench_players'])
            );
        }

        return $this->visitor;
    }

    private function playerFromArray(): callable
    {
        return function (array $data) {
            return Player::fromArray($data);
        };
    }

}