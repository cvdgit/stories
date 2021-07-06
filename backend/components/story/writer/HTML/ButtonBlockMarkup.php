<?php

namespace backend\components\story\writer\HTML;

use backend\components\story\AbstractBlock;
use backend\components\story\ButtonBlock;
use backend\components\story\writer\HTML\elements\ButtonElement;
use yii\helpers\Html;

class ButtonBlockMarkup extends AbstractMarkup
{

    public function __construct(AbstractBlock $block)
    {
        parent::__construct($block, new ButtonElement());
    }

    private function getElementMarkup(ButtonBlock $block): string
    {
        $element = $this->getElement();
        $elementAttributes = $element->getAttributes();
        $elementAttributes['href'] = $block->getUrl();
        return Html::tag($element->getTagName(), $block->getText(), $elementAttributes);
    }

    public function markup(): string
    {
        /** @var ButtonBlock $block */
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
            'data-block-type' => 'button',
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