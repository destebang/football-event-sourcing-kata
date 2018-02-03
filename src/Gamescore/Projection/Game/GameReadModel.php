<?php

namespace Football\Gamescore\Projection\Game;

use Doctrine\DBAL\Connection;
use Prooph\EventStore\Projection\AbstractReadModel;

class GameReadModel extends AbstractReadModel
{
    const TABLE_NAME = 'game';

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function init(): void
    {
        $tableName = self::TABLE_NAME;

        $sql = <<<EOT
CREATE TABLE `$tableName` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `local_team_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `local_team_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `local_team_goals` tinyint DEFAULT 0,
  `visitor_team_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `visitor_team_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visitor_team_goals` tinyint DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOT;

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    public function isInitialized(): bool
    {
        $tableName = self::TABLE_NAME;

        $sql = "SHOW TABLES LIKE '$tableName';";

        $statement = $this->connection->prepare($sql);
        $statement->execute();

        $result = $statement->fetch();

        if (false === $result) {
            return false;
        }

        return true;
    }

    public function reset(): void
    {
        $tableName = self::TABLE_NAME;

        $sql = "TRUNCATE TABLE '$tableName';";

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    public function delete(): void
    {
        $tableName = self::TABLE_NAME;

        $sql = "DROP TABLE $tableName;";

        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    public function addGoalToScore(string $id, string $localOrVisitor): void
    {
        $scoreToIncrease = $localOrVisitor . '_team_goals';
        $sql = "UPDATE game SET $scoreToIncrease = $scoreToIncrease + 1 WHERE id = :id";
        $statement = $this->connection->prepare($sql);
        $statement->bindParam('id', $id);
        $statement->execute();
    }

    public function insert(array $data): void
    {
        $this->connection->insert(self::TABLE_NAME, $data);
    }

    public function update(array $data, array $identifier): void
    {
        $this->connection->update(self::TABLE_NAME, $data, $identifier);
    }
}