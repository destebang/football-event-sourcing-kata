<?php

namespace Football\Gamescore\Projection\Game;

use Football\Common\Domain\Model\Uuid;
use Football\Gamescore\Domain\Aggregate\TeamId;
use Football\Gamescore\Domain\Event\GameStarted;
use Football\Gamescore\Domain\Event\GoalScored;
use Football\Gamescore\Domain\Event\OwnGoalScored;
use Prooph\Bundle\EventStore\Projection\ReadModelProjection;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventStore\Projection\ReadModelProjector;

class GameProjection implements ReadModelProjection
{

    public function project(ReadModelProjector $projector): ReadModelProjector
    {
        $projector->fromStream('event_stream')
            ->when([
                GameStarted::class => function ($state, GameStarted $event) {

                    /** @var GameReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack('insert', [
                        'id' => $event->gameId()->toString(),
                        'local_team_id' => $event->local()->getId()->toString(),
                        'local_team_name' => $event->local()->getName(),
                        'visitor_team_id' => $event->visitor()->getId()->toString(),
                        'visitor_team_name' => $event->visitor()->getName(),
                    ]);
                },
                GoalScored::class => function ($state, GoalScored $event) {

                    /** @var GameReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack(
                        'addGoalToScore',
                        $event->aggregateId(),
                        $event->getLocalOrVisitor()
                    );
                },
            ]);

        return $projector;
    }
}
