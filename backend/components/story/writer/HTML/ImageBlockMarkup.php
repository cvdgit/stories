<?php


namespace backend\components\story\writer\HTML;


use backend\components\story\AbstractBlock;
use backend\components\story\ImageBlock;
use backend\components\story\writer\HTML\elements\ImageElement;
use yii\helpers\Html;

class ImageBlockMarkup extends AbstractMarkup
{

    public function __construct(AbstractBlock $block)
    {
        parent::__construct($block, new ImageElement());
    }

    private function getElementMarkup(ImageBlock $block): string
    {
        $element = $this->getElement();
        return Html::tag($element->getTagName(), '', [
            'data-src' => $block->getFilePath(),
            'data-natural-width' => $block->getNaturalWidth(),
            'data-natural-height' => $block->getNaturalHeight(),
            'data-action' => $block->getAction(),
            'data-action-story' => $block->getActionStoryID(),
            'data-action-slide' => $block->getActionSlideID(),
            'data-backtonextslide' => $block->getBackToNextSlide(),
        ]);
    }

    public function markup(): string
    {
        /** @var ImageBlock $block */
        $block = $this->getBlock();

        $elementTag = $this->getElementMarkup($block);
        if ($block->getImageSource() !== '') {
            $elementTag .= Html::tag('span', $block->getImageSource(), ['class' => 'image-source']);
        }
        $contentBlockTag = Html::tag('div', $elementTag, [
            'class' => 'sl-block-content',
            'style' => $this->arrayToStyle([
                'z-index' => 11,
            ]),
        ]);
        return Html::tag('div', $contentBlockTag, [
            'class' => 'sl-block',
            'data-block-id' => $block->getId(),
            'data-block-type' => 'image',
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