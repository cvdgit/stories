<?php


namespace backend\components\story\writer\HTML;


use backend\components\story\AbstractBlock;
use backend\components\story\TestBlock;
use backend\components\story\writer\HTML\elements\TestElement;
use yii\helpers\Html;

class TestBlockMarkup extends AbstractMarkup
{

    public function __construct(AbstractBlock $block)
    {
        parent::__construct($block, new TestElement());
    }

    private function getElementMarkup(TestBlock $block): string
    {
        $element = $this->getElement();
        $elementAttributes = $element->getAttributes();
        $elementAttributes['style'] = $this->setStyleValue($elementAttributes['style'], 'font-size', $block->getFontSize());
        $elementAttributes['data-test-id'] = $block->getTestId();
        return Html::tag($element->getTagName(), $block->getText(), $elementAttributes);
    }

    public function markup(): string
    {
        /** @var TestBlock $block */
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
            'data-block-type' => AbstractBlock::TYPE_TEST,
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