<?php

declare(strict_types=1);

namespace backend\components\book;

use backend\components\book\blocks\GuestBlockInterface;
use backend\components\book\blocks\Image;
use backend\components\book\blocks\Link;
use backend\components\book\blocks\Test;
use backend\components\book\blocks\Text;
use backend\components\book\blocks\Transition;
use backend\components\book\blocks\Video;

class SlideBlocks
{
    private $params = [
        Text::class,
        Image::class,
        Test::class,
        Transition::class,
        Link::class,
        Video::class,
    ];

    /** @var array<BlockCollection> */
    private $blocks = [];

    public function __construct()
    {
        foreach ($this->params as $className) {
            $this->blocks[$className] = new BlockCollection($className);
        }
    }

    public function addGuestBlock(GuestBlockInterface $block): void
    {
        $className = get_class($block);
        $this->blocks[$className]->append($block);
    }

    public function getGuestBlocks(string $className): BlockCollection
    {
        return $this->blocks[$className];
    }

    public function isEmpty(): bool
    {
        $empty = true;
        foreach ($this->blocks as $block) {
            $empty = $empty && $block->isEmpty();
        }
        return $empty;
    }
}
