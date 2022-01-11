<?php

namespace backend\components\training\base;

class UserProgress
{

    private $history = [];
    private $stars = [];
    private $starsCount = 0;

    /**
     * @return array
     */
    public function getHistory(): array
    {
        return $this->history;
    }

    /**
     * @return array
     */
    public function getStars(): array
    {
        return $this->stars;
    }

    /**
     * @return int
     */
    public function getStarsCount(): int
    {
        return $this->starsCount;
    }

    /**
     * @param array $history
     */
    public function setHistory(array $history): void
    {
        $this->history = $history;
    }

    /**
     * @param int $starsCount
     */
    public function setStarsCount(int $starsCount): void
    {
        $this->starsCount = $starsCount;
    }

    /**
     * @param array $stars
     */
    public function setStars(array $stars): void
    {
        $this->stars = $stars;
    }
}