<?php


namespace backend\components\story;


use backend\models\editor\TransitionForm;

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

    public function getValues(): array
    {
        return array_merge([
            'text' => $this->text,
            'text_size' => $this->fontSize,
            'transition_story_id' => $this->transition_story_id,
            'slides' => $this->slides,
        ], parent::getValues());
    }

    public function create()
    {
        $block = new self();
        $block->setWidth('290px');
        $block->setHeight('50px');
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
    }

}