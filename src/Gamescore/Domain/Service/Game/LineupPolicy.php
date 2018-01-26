<?php

namespace Football\Gamescore\Domain\Service\Game;

use Football\Gamescore\Domain\Aggregate\Team;

interface LineupPolicy
{
    public function lineupIsValid(Team $team): bool;
}