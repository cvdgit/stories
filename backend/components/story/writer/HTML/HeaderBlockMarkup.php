<?php

namespace backend\components\story\writer\HTML;

use backend\components\story\AbstractBlock;
use backend\components\story\TextBlock;
use backend\components\story\writer\HTML\elements\HeaderElement;
use yii\helpers\Html;

class HeaderBlockMarkup extends TextBlockMarkup
{

    public function __construct(AbstractBlock $block)
    {
        parent::__construct($block, new HeaderElement());
    }

    public function markup(): string
    {
        /** @var TextBlock $block */
        $block = $this->getBlock();

        $element = $this->getElement();
        $elementAttributes = $element->getAttributes();
        $elementTag = Html::tag($element->getTagName(), $block->getText(), $elementAttributes);

        $contentBlockAttributes = array_merge($this->getContentBlockAttributes(), [
            'style' => $this->arrayToStyle([
                'color' => 'rgb(255, 255, 255)',
                'z-index' => 10,
                'text-align' => 'center',
            ]),
        ]);
        $contentBlockTag = Html::tag('div', $elementTag, $contentBlockAttributes);

        $blockAttributes = array_merge($this->getBlockAttributes(), [
            'style' => $this->arrayToStyle([
                'width' => $block->getWidth(),
                'height' => $block->getHeight(),
                'left' => $block->getLeft(),
                'top' => $block->getTop(),
            ]),
        ]);
        return Html::tag('div', $contentBlockTag, $blockAttributes);
    }

}