<?php

namespace Football\Game\Domain\Model\Game;

use Football\Common\Domain\Model\BaseAggregateRoot;

class Game extends BaseAggregateRoot
{
    /**
     * @var
     */
    private $local;


    private $visitor;
}