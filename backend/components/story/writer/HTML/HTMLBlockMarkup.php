<?php


namespace backend\components\story\writer\HTML;


use backend\components\story\AbstractBlock;
use backend\components\story\HTMLBLock;
use backend\components\story\writer\HTML\elements\HTMLElement;
use yii\helpers\Html;

class HTMLBlockMarkup extends AbstractMarkup
{

    public function __construct(AbstractBlock $block)
    {
        parent::__construct($block, new HTMLElement());
    }

    private function getElementMarkup(HTMLBLock $block): string
    {
        $element = $this->getElement();
        return Html::tag($element->getTagName(), $block->getContent());
    }

    public function markup(): string
    {
        /** @var HTMLBLock $block */
        $block = $this->getBlock();

        $elementTag = $this->getElementMarkup($block);
        $contentBlockTag = Html::tag('div', $elementTag, [
            'class' => 'sl-block-content',
            'style' => $this->arrayToStyle([
                'z-index' => 11,
            ]),
        ]);
        return Html::tag('div', $contentBlockTag, [
            'class' => 'sl-block',
            'data-block-id' => $block->getId(),
            'data-block-type' => 'html',
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