<?php

namespace Football\Gamescore\Domain\Aggregate;

use Football\Common\Domain\Model\BaseAggregateRoot;
use Football\Gamescore\Domain\Event\GameStarted;
use Football\Gamescore\Domain\Event\GoalScored;
use Football\Gamescore\Domain\Event\OwnGoalScored;
use Football\Gamescore\Domain\Service\Game\LineupPolicy;
use Football\Gamescore\Domain\ValueObject\Score;
use function Functional\first;
use Prooph\EventSourcing\AggregateChanged;

class Game extends BaseAggregateRoot
{
    /**
     * @var Team
     */
    private $local;

    /**
     * @var Team
     */
    private $visitor;

    /**
     * @var Score
     */
    private $score;

    public static function startGame(
        GameId $gameId,
        Team $local,
        Team $visitor,
        LineupPolicy $lineupPolicy
    ): self {

        $game = new Game();
        $game->checkLineupPolicy($local, $visitor, $lineupPolicy);
        $game->recordThat(GameStarted::withTeams($gameId, $local, $visitor));

        return $game;
    }

    public function scoreGoal(PlayerId $playerId): void
    {
        $playerOnGame = $this->getOnGamePlayer($playerId);

        $this->recordThat(
            GoalScored::byPlayer(
                $this->id,
                $this->getPlayerTeam($playerOnGame),
                $playerOnGame
            )
        );
    }

    public function scoreOwnGoal(PlayerId $playerId): void
    {
        $playerOnGame = $this->getOnGamePlayer($playerId);
        $scoringTeam = $this->getPlayerTeam($playerOnGame) === $this->visitor ? $this->local : $this->visitor;

        $this->recordThat(
            OwnGoalScored::byPlayer(
                $this->id,
                $scoringTeam,
                $playerOnGame
            )
        );
    }

    private function checkLineupPolicy(Team $local, Team $visitor, LineupPolicy $lineupPolicy): void
    {
        if (!$lineupPolicy->lineupIsValid($local) || !$lineupPolicy->lineupIsValid($visitor)) {
            throw new \InvalidArgumentException('Line up policy not fulfilled');
        }
    }

    public function whenGameStarted(GameStarted $gameStarted): void
    {
        $this->id = GameId::fromString($gameStarted->aggregateId());
        $this->local = $gameStarted->local();
        $this->visitor = $gameStarted->visitor();
        $this->score = Score::initScore();
    }

    public function whenGoalScored(GoalScored $goalScored): void
    {
        $this->updateScoreFromGoalScored($goalScored);
    }

    public function whenOwnGoalScored(OwnGoalScored $goalScored): void
    {
        $this->updateScoreFromGoalScored($goalScored);
    }

    public function getScore(): Score
    {
        return $this->score;
    }

    public function getLocal(): Team
    {
        return $this->local;
    }

    public function getVisitor(): Team
    {
        return $this->visitor;
    }

    private function getOnGamePlayer(PlayerId $playerId): ?Player
    {
        $allPlayers = array_merge(
            $this->local->getOnGamePlayers(),
            $this->visitor->getOnGamePlayers()
        );

        $playerOnGame = first($allPlayers, function (Player $player) use ($playerId) {
            return $player->getId()->equals($playerId);
        });

        if (!$playerOnGame) {
            throw new \DomainException("Player with id given is not currently playing on the game");
        }

        return $playerOnGame;
    }

    private function getPlayerTeam(Player $player): Team
    {
        if ($this->local->playerIsOnTeam($player)) {
            return $this->local;
        } elseif ($this->visitor->playerIsOnTeam($player)) {
            return $this->visitor;
        }

        throw new \DomainException("Player is not in the game");
    }

    /**
     * @param AggregateChanged|GoalScored|OwnGoalScored $goalScored
     */
    private function updateScoreFromGoalScored(AggregateChanged $goalScored): void
    {
        if ($goalScored->getScoringTeamId()->equals($this->visitor->getId())) {
            $this->score = $this->score->visitorScoreGoal();
        } else {
            $this->score = $this->score->localScoreGoal();
        }
    }
}