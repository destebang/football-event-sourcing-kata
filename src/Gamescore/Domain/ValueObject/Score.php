<?php

namespace Football\Gamescore\Domain\ValueObject;

class Score
{
    /**
     * @var int
     */
    private $localScore;

    /**
     * @var int
     */
    private $visitorScore;

    private function __construct(int $localScore, int $visitorScore)
    {
        $this->localScore = $localScore;
        $this->visitorScore = $visitorScore;
    }

    public static function initScore(): self
    {
        return new self(0, 0);
    }

    public function localScoreGoal(): Score
    {
        $newLocalScore = $this->localScore + 1;
        return new self($newLocalScore, $this->visitorScore);
    }

    public function visitorScoreGoal(): Score
    {
        $newVisitorScore = $this->visitorScore + 1;
        return new self($this->localScore, $newVisitorScore);
    }

    public function equals(Score $score): bool
    {
        return $this->localScore === $score->localScore
            && $this->visitorScore = $score->visitorScore;
    }
}