<?php

namespace Football\Gamescore\Domain\Event;

use Football\Gamescore\Domain\Aggregate\GameId;
use Football\Gamescore\Domain\Aggregate\Player;
use Football\Gamescore\Domain\Aggregate\PlayerId;
use Football\Gamescore\Domain\Aggregate\Team;
use Football\Gamescore\Domain\Aggregate\TeamId;
use Prooph\EventSourcing\AggregateChanged;

class GoalScored extends AggregateChanged
{
    private const TEAM_ID_KEY = 'team_id';
    private const PLAYER_ID_KEY = 'player_id';

    /**
     * @var TeamId
     */
    private $teamId;

    /**
     * @var PlayerId
     */
    private $playerId;

    public static function byPlayer(GameId $gameId, Team $team, Player $player): GoalScored
    {
        /** @var self $event */
        $event = self::occur($gameId->toString(), [
            self::TEAM_ID_KEY => $team->getId(),
            self::PLAYER_ID_KEY => $player->getId()
        ]);

        $event->teamId = $team->getId();
        $event->playerId = $player->getId();

        return $event;
    }

    public function getTeamId(): TeamId
    {
        if (!$this->teamId) {
            $this->teamId = TeamId::fromString($this->payload[self::TEAM_ID_KEY]);
        }

        return $this->teamId;
    }

    public function getPlayerId(): PlayerId
    {
        if (!$this->playerId) {
            $this->playerId = PlayerId::fromString($this->payload[self::PLAYER_ID_KEY]);
        }
        return $this->playerId;
    }
}
