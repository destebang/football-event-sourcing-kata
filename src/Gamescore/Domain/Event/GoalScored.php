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
    protected const SCORING_TEAM_ID_KEY = 'scoring_team_id';
    protected const PLAYER_ID_KEY = 'player_id';
    protected const LOCAL_OR_VISITOR_KEY = 'local_or_visitor';

    /**
     * @var TeamId
     */
    protected $scoringTeam;

    /**
     * @var PlayerId
     */
    protected $playerId;

    /**
     * @var string
     */
    protected $localOrVisitor;

    public static function byPlayer(GameId $gameId, Team $scoringTeam, Player $player, string $localOrVisitor): GoalScored
    {
        /** @var self $event */
        $event = static::occur($gameId->toString(), [
            self::SCORING_TEAM_ID_KEY => $scoringTeam->getId()->toString(),
            self::PLAYER_ID_KEY => $player->getId()->toString(),
            self::LOCAL_OR_VISITOR_KEY => $localOrVisitor
        ]);

        $event->scoringTeam = $scoringTeam->getId();
        $event->playerId = $player->getId();
        $event->localOrVisitor = $localOrVisitor;

        return $event;
    }

    public function getScoringTeamId(): TeamId
    {
        if (!$this->scoringTeam) {
            $this->scoringTeam = TeamId::fromString($this->payload[self::SCORING_TEAM_ID_KEY]);
        }

        return $this->scoringTeam;
    }

    public function getPlayerId(): PlayerId
    {
        if (!$this->playerId) {
            $this->playerId = PlayerId::fromString($this->payload[self::PLAYER_ID_KEY]);
        }
        return $this->playerId;
    }

    public function getLocalOrVisitor(): string
    {
        if (!$this->localOrVisitor) {
            $this->localOrVisitor = $this->payload[self::LOCAL_OR_VISITOR_KEY];
        }

        return $this->localOrVisitor;
    }
}
