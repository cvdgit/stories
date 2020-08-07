<?php

namespace backend\components\book;

use backend\components\book\blocks\HtmlTest;
use backend\components\book\blocks\Image;
use backend\components\book\blocks\Link;
use backend\components\book\blocks\Test;
use backend\components\book\blocks\Text;
use backend\components\book\blocks\Transition;
use backend\components\book\blocks\Video;

class SlideBlocks
{

    protected $params = [
        'texts' => Text::class,
        'images' => Image::class,
        'htmltests' => HtmlTest::class,
        'tests' => Test::class,
        'transitions' => Transition::class,
        'links' => Link::class,
        'videos' => Video::class,
    ];

    /** @var BlockCollection[] */
    protected $blocks = [];

    public function __construct()
    {
        foreach ($this->params as $blocksName => $className) {
            $this->blocks[$blocksName] = new BlockCollection($className);
        }
    }

    public function __get(string $name)
    {
        if (isset($this->blocks[$name])) {
            return $this->blocks[$name];
        }
        return null;
    }

    public function __call($name, $arguments)
    {
        $blockName = strtolower(str_replace('create', '', $name));
        if (isset($this->blocks[$blockName])) {
            $blockCollection = $this->blocks[$blockName];
            $blockCollection->createBlock($arguments);
        }
        else {
            $this->$name($arguments);
        }
    }

    public function isEmpty()
    {
        $empty = true;
        foreach ($this->blocks as $block) {
            $empty = $empty && $block->isEmpty();
        }
        return $empty;
    }

    public function getBlocks()
    {
        return $this->blocks;
    }

}