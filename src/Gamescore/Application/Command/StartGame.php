<?php

namespace Football\Gamescore\Application\Command;

use Assert\Assertion;
use Football\Gamescore\Domain\Aggregate\GameId;
use Football\Gamescore\Domain\Aggregate\Team;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

final class StartGame extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public static function withTeams(string $gameId, array $localTeam, array $visitorTeam): self
    {
        return new self([
            'game_id' => $gameId,
            'local_team' => $localTeam,
            'visitor_team_id' => $visitorTeam
        ]);
    }

    public function gameId(): GameId
    {
         return GameId::fromString($this->payload['game_id']);
    }

    public function localTeam(): Team
    {
        return Team::fromArray($this->payload['local_team']);
    }

    public function visitorTeam(): Team
    {

        return Team::fromArray($this->payload['visitor_team']);
    }

    protected function setPayload(array $payload): void
    {
        Assertion::keyExists($payload, 'game_id');
        Assertion::uuid($payload['game_id']);
        Assertion::keyExists($payload, 'local_team');
        Assertion::isArray($payload, 'local_team');
        Assertion::keyExists($payload, 'visitor_team');
        Assertion::isArray($payload, 'local_team');

        $this->payload = $payload;
    }
}
