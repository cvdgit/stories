<?php

namespace backend\components\story\writer\HTML;

use backend\components\story\AbstractBlock;
use backend\components\story\TextBlock;
use backend\components\story\writer\HTML\elements\ParagraphElement;
use yii\helpers\Html;

class ParagraphBlockMarkup extends TextBlockMarkup
{

    public function __construct(AbstractBlock $block)
    {
        parent::__construct($block, new ParagraphElement());
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
                'z-index' => 12,
                'text-align' => 'left',
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
