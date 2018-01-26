<?php

namespace Football\Common\Domain\Model;

use Ramsey\Uuid\Uuid as RamseyUuid;

abstract class Uuid
{
    /**
     * @var RamseyUuid
     */
    private $uuid;

    public static function generate(): Uuid
    {
        return new static(RamseyUuid::uuid4());
    }


    public static function fromString(string $userId): Uuid
    {
        return new static(RamseyUuid::fromString($userId));
    }

    protected function __construct(RamseyUuid $uuid)
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
