<?php

namespace Football\Gamescore\Projection\Game;

use Doctrine\DBAL\Connection;
use Football\Gamescore\Projection\Table;

class GameFinder
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function findById(string $gameId): \stdClass
    {
        $stmt = $this->connection->prepare(sprintf('SELECT * FROM %s where id = :game_id', Table::GAME));
        $stmt->bindValue('game_id', $gameId);
        $stmt->execute();

        $result = $stmt->fetch();

        if (false === $result) {
            return null;
        }

        return $result;
    }

    public function findByTeamId(string $teamId): array
    {
        return $this->connection->fetchAll(
            sprintf('SELECT * FROM %s WHERE local_team_id = :local_team_id OR visitor_team_id = :visitor_team_id', Table::GAME),
            ['local_team_id' => $teamId, 'visitor_team_id' => $teamId]
        );
    }
}