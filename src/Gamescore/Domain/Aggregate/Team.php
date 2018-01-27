<?php

namespace Football\Gamescore\Domain\Aggregate;

use Football\Common\Domain\Model\Uuid;

class Team
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $onGamePlayers;

    /**
     * @var string[]
     */
    private $benchPlayers;

    public function __construct(
        Uuid $id,
        string $name,
        array $onGamePlayers,
        array $benchPlayers
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->onGamePlayers = $onGamePlayers;
        $this->benchPlayers = $benchPlayers;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Player[]
     */
    public function getOnGamePlayers(): array
    {
        return $this->onGamePlayers;
    }

    /**
     * @return Player[]
     */
    public function getBenchPlayers(): array
    {
        return $this->benchPlayers;
    }

    public function playerIsOnTeam(Player $player): bool
    {
        return in_array($player, array_merge($this->onGamePlayers, $this->benchPlayers));
    }

    public function toArray()
    {
        $playerToArray = function (Player $player) {
            return $player->toArray();
        };

        return [
            'id' => $this->id->toString(),
            'name' => $this->name,
            'on_game_players' => array_map(
                $playerToArray,
                $this->onGamePlayers
            ),
            'bench_players' => array_map(
                $playerToArray,
                $this->benchPlayers
            ),
        ];
    }

    public static function fromArray(array $teamData): self
    {
        $playerFromArray = function (array $playerData) {
            return Player::fromArray($playerData);
        };

        return new Team(
            TeamId::fromString($teamData['id']),
            $teamData['name'],
            array_map($playerFromArray, $teamData['on_game_players']),
            array_map($playerFromArray, $teamData['bench_players'])
        );
    }
}
