<?php

namespace Football\Gamescore\Domain\Aggregate;

use Football\Common\Domain\Model\Uuid;

class Player
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
     * @var int
     */
    private $number;

    /**
     * @var int
     */
    private $yellowCardsReceived;

    /**
     * @var int
     */
    private $redCardsReceived;

    public function __construct(Uuid $id, string $name, int $number)
    {
        $this->id = $id;
        $this->name = $name;
        $this->number = $number;
        $this->yellowCardsReceived = 0;
        $this->redCardsReceived = 0;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name,
            'number' => $this->number
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            PlayerId::fromString($data['id']),
            $data['name'],
            $data['number']
        );
    }
}
