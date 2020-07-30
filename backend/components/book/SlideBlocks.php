<?php

namespace backend\components\book;

use backend\components\book\blocks\AbstractTest;
use backend\components\book\blocks\Image;
use backend\components\book\blocks\Link;
use backend\components\book\blocks\Text;
use backend\components\book\blocks\Transition;

class SlideBlocks
{

    /** @var $texts Text[] */
    protected $texts = [];

    /** @var $images Image[] */
    protected $images = [];

    /** @var $tests AbstractTest[] */
    protected $tests = [];

    /** @var $transitions Transition[] */
    protected $transitions = [];

    /** @var $links Link[] */
    protected $links = [];

    public function addText(Text $text)
    {
        $this->texts[] = $text;
    }

    public function addImage(Image $image)
    {
        $this->images[] = $image;
    }

    public function addTest(AbstractTest $test)
    {
        $this->tests[] = $test;
    }

    public function addTransition(Transition $transition)
    {
        $this->transitions[] = $transition;
    }

    public function addLink(Link $link)
    {
        $this->links[] = $link;
    }

    public function isEmpty()
    {
        return !$this->haveTexts()
            && !$this->haveImages()
            && !$this->haveTests()
            && !$this->haveTransitions()
            && !$this->haveLinks();
    }

    public function getBlocks()
    {
        return [
            'images' => $this->images,
            'texts' => $this->texts,
            'tests' => $this->tests,
            'transitions' => $this->transitions,
            'links' => $this->links,
        ];
    }

    /**
     * @return Text[]
     */
    public function getTexts(): array
    {
        return $this->texts;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @return AbstractTest[]
     */
    public function getTests(): array
    {
        return $this->tests;
    }

    /**
     * @return Transition[]
     */
    public function getTransitions(): array
    {
        return $this->transitions;
    }

    /**
     * @return Link[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    public function haveTexts()
    {
        return count($this->texts) !== 0;
    }

    public function haveImages()
    {
        return count($this->images) !== 0;
    }

    public function haveTests()
    {
        return count($this->tests) !== 0;
    }

    public function haveTransitions()
    {
        return count($this->transitions) !== 0;
    }

    public function haveLinks()
    {
        return count($this->links) !== 0;
    }

}