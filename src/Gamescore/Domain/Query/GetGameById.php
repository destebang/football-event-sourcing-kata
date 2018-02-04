<?php

namespace Football\Gamescore\Domain\Query;

class GetGameById
{
    /**
     * @var string
     */
    private $gameId;

    public function __construct(string $gameId)
    {
        $this->gameId = $gameId;
    }

    public function gameId(): string
    {
        return $this->gameId;
    }
}