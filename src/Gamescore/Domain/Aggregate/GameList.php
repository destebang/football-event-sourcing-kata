<?php

namespace Football\Gamescore\Domain\Aggregate;

interface GameList
{
    public function get(GameId $gameId): Game;

    public function save(Game $game): void;
}
