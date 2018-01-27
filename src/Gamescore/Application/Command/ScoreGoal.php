<?php

namespace Football\Gamescore\Application\Command;

use Assert\Assertion;
use Football\Gamescore\Domain\Aggregate\GameId;
use Football\Gamescore\Domain\Aggregate\PlayerId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ScoreGoal extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public static function playerInGame(string $gameId, string $playerId): self
    {
        return new self([
            'game_id' => $gameId,
            'player_id' => $playerId
        ]);
    }

    public function gameId(): GameId
    {
        return GameId::fromString($this->payload['game_id']);
    }

    public function playerId(): PlayerId
    {
        return PlayerId::fromString($this->payload['player_id']);
    }


    protected function setPayload(array $payload): void
    {
        Assertion::keyExists($payload, 'game_id');
        Assertion::uuid($payload['game_id']);
        Assertion::keyExists($payload, 'player_id');
        Assertion::uuid($payload['player_id']);

        $this->payload = $payload;
    }
}