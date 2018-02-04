<?php

namespace Football\Gamescore\UserInterface\Controller;

use Football\Gamescore\Domain\Query\GetGameById;
use Football\Gamescore\Domain\Query\GetGamesByTeamId;
use Prooph\ServiceBus\QueryBus;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class GameQueryController extends Controller
{
    /**
     * @var QueryBus
     */
    private $queryBus;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(QueryBus $queryBus, LoggerInterface $logger)
    {

        $this->queryBus = $queryBus;
        $this->logger = $logger;
    }

    public function getGame(string $gameId)
    {
        $game = null;
        $this->queryBus
            ->dispatch(new GetGameById($gameId))
            ->then(
                function (\stdClass $result = null) use (&$game) {
                    $game = $result;
                }
            )->otherwise(
                function() {
                    return JsonResponse::create('Error getting result', 500);
                }
            );

        return JsonResponse::create($game);
    }

    public function getGamesByTeam(string $teamId)
    {
        $games = [];
        $this->queryBus
            ->dispatch(new GetGamesByTeamId($teamId))
            ->then(
                function (\stdClass $result = null) use (&$games) {
                    $games = $result;
                }
            );

        return JsonResponse::create($games);
    }
}