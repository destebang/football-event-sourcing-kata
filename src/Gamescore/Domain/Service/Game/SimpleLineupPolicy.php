<?php

namespace Football\Gamescore\Domain\Service\Game;

use Football\Gamescore\Domain\Aggregate\Player;
use Football\Gamescore\Domain\Aggregate\Team;

class SimpleLineupPolicy implements LineupPolicy
{
    const STARTING_PLAYERS_NUMBER = 11;
    const MAXIMUM_BENCH_PLAYERS_NUMBER = 6;

    public function lineupIsValid(Team $team): bool
    {
        return true;
        /*return $this->noPlayerIsRepeated($team)
            && $this->gamePlayersNumberIsTheExpected($team->getOnGamePlayers())
            && $this->benchPlayersNumberIsNotOverTheMaximum($team->getBenchPlayers());*/
    }

    private function noPlayerIsRepeated(Team $team): bool
    {
        $allPlayers = array_merge($team->getOnGamePlayers(), $team->getBenchPlayers());

        return count($allPlayers) === count(array_unique($allPlayers));
    }

    /**
     * @param array|Player[] $onGamePlayers
     * @return bool
     */
    private function gamePlayersNumberIsTheExpected(array $onGamePlayers): bool
    {
        return count($onGamePlayers) === self::STARTING_PLAYERS_NUMBER;
    }

    /**
     * @param array|Player[] $benchPlayers
     * @return bool
     */
    private function benchPlayersNumberIsNotOverTheMaximum(array $benchPlayers): bool
    {
        return count($benchPlayers) <= self::MAXIMUM_BENCH_PLAYERS_NUMBER;
    }
}
