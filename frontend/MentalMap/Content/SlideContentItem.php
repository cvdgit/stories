<?php

declare(strict_types=1);

namespace frontend\MentalMap\Content;

use Ramsey\Uuid\UuidInterface;

class SlideContentItem
{
    /**
     * @var int
     */
    private $slideId;
    /**
     * @var UuidInterface
     */
    private $mentalMapId;
    /**
     * @var bool
     */
    private $required;
    /**
     * @var string
     */
    private $blockId;

    public function __construct(int $slideId, UuidInterface $mentalMapId, string $blockId, bool $required)
    {
        $this->slideId = $slideId;
        $this->mentalMapId = $mentalMapId;
        $this->required = $required;
        $this->blockId = $blockId;
    }

    public function getSlideId(): int
    {
        return $this->slideId;
    }

    public function getMentalMapId(): UuidInterface
    {
        return $this->mentalMapId;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getBlockId(): string
    {
        return $this->blockId;
    }
}
