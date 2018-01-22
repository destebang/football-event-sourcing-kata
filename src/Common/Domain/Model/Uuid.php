<?php

namespace Football\Common\Domain\Model;

abstract class Uuid
{
    /**
     * @var Uuid
     */
    private $uuid;

    public static function generate(): Uuid
    {
        return new static(Uuid::uuid4());
    }


    public static function fromString(string $userId): Uuid
    {
        return new static(Uuid::fromString($userId));
    }

    protected function __construct(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->uuid->toString();
    }

    public function equals(Uuid $other): bool
    {
        return $this->toString() === $other->toString();
    }
}
