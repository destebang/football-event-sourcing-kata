<?php

namespace Football\Gamescore\Domain\Aggregate;

use Football\Gamescore\Domain\Event\GameStarted;
use Football\Gamescore\Domain\Event\GoalScored;
use Football\Gamescore\Domain\Service\Game\LineupPolicy;
use Football\Gamescore\Domain\ValueObject\Score;
use PHPUnit\Framework\TestCase;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class GameTest extends TestCase
{
    private const ON_GAME_PLAYER = '1d724795-9e0c-47a0-a2c4-80af933a2312';
    const ON_BENCH_PLAYER = '1243bf9e-72a6-47cf-9e6d-dcf3e0b03472';
    /**
     * @var LineupPolicy|ObjectProphecy
     */
    private $lineupPolicy;

    /**
     * @var GameId
     */
    private $gameId;

    /**
     * @var AggregateChanged[]
     */
    private $pastEvents;

    /**
     * @var AggregateChanged[]
     */
    private $recordedEvents;

    /**
     * @var Game
     */
    private $game;

    protected function setUp()
    {
        $this->lineupPolicy = $this->prophesize(LineupPolicy::class);
        $this->lineupPolicy->lineupIsValid(Argument::any())->willReturn(true);
        $this->gameId = GameId::generate();
    }

    /**
     * @dataProvider invalidTeamsProvider
     * @test
     */
    public function givenAnyNoValidTeamAnEventGameStartedWasNotTriggered(
        Team $local,
        Team $visitor,
        bool $localFulfillsPolicy,
        bool $visitorFulfillsPolicy
    ): void {
        $this->lineupPolicy->lineupIsValid($local)->willReturn($localFulfillsPolicy);
        $this->lineupPolicy->lineupIsValid($visitor)->willReturn($visitorFulfillsPolicy);

        $this->expectException(\InvalidArgumentException::class);
        Game::startGame($this->gameId, $local, $visitor, $this->lineupPolicy->reveal());
    }

    /**
     * @test
     */
    public function givenValidTeamsExpectedGameWasReturned(): void
    {
        $local = new Team(TeamId::generate(),'CF Gava', [], []);
        $visitor = new Team(TeamId::generate(),'CE Viladecans', [], []);

        $this->game = Game::startGame($this->gameId, $local, $visitor, $this->lineupPolicy->reveal());

        $this->assertInstanceOf(GameStarted::class, $this->popNextRecordedEvent());
        $this->assertEquals($local, $this->game->getLocal());
        $this->assertEquals($visitor, $this->game->getVisitor());
    }

    /**
     * @test
     */
    public function givenAGameAPlayerOnTheBenchCanNotScoreAGoal(): void
    {
        $this->givenAGameWasStarted();

        $this->expectException(\DomainException::class);
        $this->game()->scoreGoal(PlayerId::fromString(self::ON_BENCH_PLAYER));
    }

    /**
     * @test
     */
    public function givenANotOfTheGamePlayerItCanNotScoreAGoal(): void
    {
        $this->givenAGameWasStarted();

        $this->expectException(\DomainException::class);
        $this->game()->scoreGoal(PlayerId::generate());
    }

    /**
     * @test
     */
    public function givenAPlayerInTheGameAGoalCanBeScored(): void
    {
        $this->givenAGameWasStarted();

        $this->game()->scoreGoal(PlayerId::fromString(self::ON_GAME_PLAYER));
        $this->assertInstanceOf(GoalScored::class, $this->popNextRecordedEvent());

        $this->assertEquals(
            (Score::initScore())->localScoreGoal(),
            $this->game()->getScore()
        );
    }

    public function invalidTeamsProvider(): array
    {
        return [
            'local team is not valid' => [
                'local' => new Team(TeamId::generate(), 'CF Gava', [], []),
                'visitor' => new Team(TeamId::generate(), 'CE Viladecans', [], []),
                'local fulfills rules' => false,
                'visitor fulfills rules' => true,
            ],
            'visitor team is not valid' => [
                'local' => new Team(TeamId::generate(), 'CF Gava', [], []),
                'visitor' => new Team(TeamId::generate(), 'CE Viladecans', [], []),
                'local fulfills rules' => true,
                'visitor fulfills rules' => false,
            ],
        ];
    }

    private function givenAGameWasStarted(): void
    {
        $this->pastEvents[] = GameStarted::occur(
            GameId::generate()->toString(),
            [
                'name' => 'Something',
                'local' => [
                    'id' => TeamId::generate()->toString(),
                    'name' => 'Leganes',
                    'starting_players' => [
                        [
                            'id' => PlayerId::fromString(self::ON_GAME_PLAYER)->toString(),
                            'name' => 'Eraso',
                            'number' => 9
                        ],
                    ],
                    'bench_players' => [
                        [
                            'id' => PlayerId::fromString(self::ON_BENCH_PLAYER)->toString(),
                            'name' => 'Trolete',
                            'number' => 27
                        ],
                    ]
                ],
                'visitor' => [
                    'id' => TeamId::generate()->toString(),
                    'name' => 'Alcorcon',
                    'starting_players' => [],
                    'bench_players' => []
                ]
            ]
        );
    }

    private function game() : Game
    {
        if (! $this->game) {
            $this->game = (new AggregateTranslator())
                ->reconstituteAggregateFromHistory(
                    AggregateType::fromAggregateRootClass(Game::class),
                    new \ArrayIterator($this->pastEvents)
                );
        }

        return $this->game;
    }

    private function popNextRecordedEvent() : AggregateChanged
    {
        if (null === $this->recordedEvents) {
            $this->recordedEvents = (new AggregateTranslator)
                ->extractPendingStreamEvents($this->game());
        }

        return \array_shift($this->recordedEvents);
    }
}
