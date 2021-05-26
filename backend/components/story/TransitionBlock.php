<?php


namespace backend\components\story;


use backend\models\editor\TransitionForm;

class TransitionBlock extends ButtonBlock
{

    /** @var integer */
    protected $transition_story_id;

    /** @var string */
    protected $slides;

    /** @var int */
    protected $back_to_next_slide;

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

    public function getValues(): array
    {
        return array_merge([
            'text' => $this->text,
            'text_size' => $this->fontSize,
            'transition_story_id' => $this->transition_story_id,
            'slides' => $this->slides,
            'back_to_next_slide' => $this->back_to_next_slide,
        ], parent::getValues());
    }

    public function create()
    {
        $block = new self();
        $block->setWidth('auto');
        $block->setHeight('auto');
        $block->setTop('600px');
        $block->setLeft('990px');
        $block->setText('Название');
        $block->setFontSize('1em');
        $block->setUrl('#');
        return $block;
    }

    /**
     * @param TransitionForm $form
     */
    public function update($form)
    {
        $this->setSizeAndPosition($form->width, $form->height, $form->left, $form->top);
        $this->text = $form->text;
        $this->fontSize = $form->text_size;
        $this->transition_story_id = $form->transition_story_id;
        $this->slides = $form->slides;
        $this->back_to_next_slide = $form->back_to_next_slide;
    }

    /**
     * @return int
     */
    public function getBackToNextSlide()
    {
        return $this->back_to_next_slide;
    }

    /**
     * @param int $back_to_next_slide
     */
    public function setBackToNextSlide($back_to_next_slide): void
    {
        $this->back_to_next_slide = $back_to_next_slide;
    }

}