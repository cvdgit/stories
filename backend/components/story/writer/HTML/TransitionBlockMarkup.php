<?php

namespace backend\components\story\writer\HTML;

use backend\components\story\AbstractBlock;
use backend\components\story\TransitionBlock;
use backend\components\story\writer\HTML\elements\TransitionElement;
use yii\helpers\Html;

class TransitionBlockMarkup extends AbstractMarkup
{

    public function __construct(AbstractBlock $block)
    {
        parent::__construct($block, new TransitionElement());
    }

    private function getElementMarkup(TransitionBlock $block): string
    {
        $element = $this->getElement();
        $elementAttributes = $element->getAttributes();
        $elementAttributes['data-story-id'] = $block->getTransitionStoryId();
        $elementAttributes['data-slides'] = $block->getSlides();
        $elementAttributes['data-backtonextslide'] = $block->getBackToNextSlide();
        return Html::tag($element->getTagName(), $block->getText(), $elementAttributes);
    }

    public function markup(): string
    {
        /** @var TransitionBlock $block */
        $block = $this->getBlock();

        $elementTag = $this->getElementMarkup($block);
        $contentBlockTag = Html::tag('div', $elementTag, [
            'class' => 'sl-block-content',
            'style' => $this->arrayToStyle([
                'z-index' => 15,
            ]),
        ]);
        return Html::tag('div', $contentBlockTag, [
            'class' => 'sl-block',
            'data-block-id' => $block->getId(),
            'data-block-type' => AbstractBlock::TYPE_TRANSITION,
            'style' => $this->arrayToStyle([
                'min-width' => '4px',
                'min-height' => '4px',
                'width' => $block->getWidth(),
                'height' => $block->getHeight(),
                'left' => $block->getLeft(),
                'top' => $block->getTop(),
            ]),
        ]);
    }

}