<?php

declare(strict_types=1);

namespace backend\MentalMap\FetchMentalMapUserHistory;

class MentalMapUserHistoryItem
{
    /**
     * @var string
     */
    private $mentalMapId;
    /**
     * @var string
     */
    private $imageFragmentId;
    /**
     * @var int
     */
    private $all;
    /**
     * @var int
     */
    private $hiding;
    /**
     * @var int
     */
    private $target;
    /**
     * @var string
     */
    private $content;
    /**
     * @var int
     */
    private $createdAt;

    public function __construct(
        string $mentalMapId,
        string $imageFragmentId,
        int $all,
        int $hiding,
        int $target,
        string $content,
        int $createdAt
    ) {
        $this->mentalMapId = $mentalMapId;
        $this->imageFragmentId = $imageFragmentId;
        $this->all = $all;
        $this->hiding = $hiding;
        $this->target = $target;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }

    public function getMentalMapId(): string
    {
        return $this->mentalMapId;
    }

    public function getImageFragmentId(): string
    {
        return $this->imageFragmentId;
    }

    public function getAll(): int
    {
        return $this->all;
    }

    public function getHiding(): int
    {
        return $this->hiding;
    }

    public function getTarget(): int
    {
        return $this->target;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }
}
