<?php

namespace Football\Game\Domain\Model\Player;

use Football\Common\Domain\Model\BaseAggregateRoot;
use Prooph\EventSourcing\AggregateChanged;

class Player extends BaseAggregateRoot
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \DateTimeImmutable
     */
    private $birthDate;
}