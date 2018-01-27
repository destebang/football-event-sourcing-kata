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
    private const SCORING_TEAM_ID_KEY = 'scoring_team_id';
    private const PLAYER_ID_KEY = 'player_id';

    /**
     * @var TeamId
     */
    private $scoringTeamId;

    /**
     * @var PlayerId
     */
    private $playerId;

    public static function byPlayer(GameId $gameId, Team $team, Player $player): GoalScored
    {
        /** @var self $event */
        $event = self::occur($gameId->toString(), [
            self::SCORING_TEAM_ID_KEY => $team->getId()->toString(),
            self::PLAYER_ID_KEY => $player->getId()->toString()
        ]);

        $event->scoringTeamId = $team->getId();
        $event->playerId = $player->getId();

        return $event;
    }

    public function getScoringTeamId(): TeamId
    {
        if (!$this->scoringTeamId) {
            $this->scoringTeamId = TeamId::fromString($this->payload[self::SCORING_TEAM_ID_KEY]);
        }

        return $this->scoringTeamId;
    }

    public function getPlayerId(): PlayerId
    {
        if (!$this->playerId) {
            $this->playerId = PlayerId::fromString($this->payload[self::PLAYER_ID_KEY]);
        }
        return $this->playerId;
    }
}
