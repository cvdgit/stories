<?php


namespace backend\components\story;


class TransitionBlock extends ButtonBlock
{

    /** @var integer */
    protected $transition_story_id;

    /** @var string */
    protected $slides;

    protected $type = AbstractBlock::TYPE_TRANSITION;

    /**
     * @return int
     */
    public function getTransitionStoryId()
    {
        return $this->transition_story_id;
    }

    /**
     * @param int $transition_story_id
     */
    public function setTransitionStoryId($transition_story_id): void
    {
        $this->transition_story_id = $transition_story_id;
    }

    /**
     * @return string
     */
    public function getSlides()
    {
        return $this->slides;
    }

    /**
     * @param string $slides
     */
    public function setSlides($slides): void
    {
        $this->slides = $slides;
    }

}