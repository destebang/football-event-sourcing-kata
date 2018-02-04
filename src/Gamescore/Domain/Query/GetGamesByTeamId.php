<?php

namespace Football\Gamescore\Domain\Query;

class GetGamesByTeamId
{
    /**
     * @var string
     */
    private $teamId;

    public function __construct(string $teamId)
    {
        $this->teamId = $teamId;
    }

    public function teamId(): string
    {
        return $this->teamId;
    }
}