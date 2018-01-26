<?php

namespace Football\Gamescore\Domain\Service\Game;

use Football\Gamescore\Domain\Aggregate\Team;

class SimpleLineupPolicy implements LineupPolicy
{
    const STARTING_PLAYERS_NUMBER = 11;

    const MAXIMUM_BENCH_PLAYERS_NUMBER = 6;

    public function lineupIsValid(Team $team): bool
    {
        return count($team->getOnGamePlayers()) === self::STARTING_PLAYERS_NUMBER
            && count($team->getBenchPlayers()) <= self::MAXIMUM_BENCH_PLAYERS_NUMBER;
    }
}
