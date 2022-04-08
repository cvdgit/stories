<?php

namespace backend\components\story\writer\HTML;

use backend\components\story\AbstractBlock;
use backend\components\story\ImageBlock;
use backend\components\story\writer\HTML\elements\ImageElement;
use yii\helpers\Html;

class ImageBlockMarkup extends AbstractMarkup
{

    public function __construct(ImageBlock $block)
    {
        parent::__construct($block, new ImageElement());
    }

    private function getElementMarkup(ImageBlock $block): string
    {
        if (($filePath = $block->getFilePath()) === null) {
            return '';
        }
        $element = $this->getElement();
        $options = $block->getElementAttributes();
        $options['data-src'] = $filePath;
        $options['data-natural-width'] = $block->getNaturalWidth();
        $options['data-natural-height'] = $block->getNaturalHeight();
        $options['data-action'] = $block->getAction();
        $options['data-action-story'] = $block->getActionStoryID();
        $options['data-action-slide'] = $block->getActionSlideID();
        $options['data-backtonextslide'] = $block->getBackToNextSlide();
        return Html::tag($element->getTagName(), '', $options);
    }

    public function markup(): string
    {

        /** @var ImageBlock $block */
        $block = $this->getBlock();

        $content = [
            $this->getElementMarkup($block),
        ];

        if ($block->getImageSource() !== '') {
            $content[] = Html::tag('span', $block->getImageSource(), ['class' => 'image-source']);
        }

        $contentBlock = [
            Html::tag('div', implode("\n", $content), [
                'class' => 'sl-block-content',
                'style' => $this->arrayToStyle([
                    'z-index' => 11,
                ]),
            ]),
        ];

        if (($description = $block->getDescription()) !== '') {
            $contentBlock[] = Html::tag('div', $description, [
                'class' => 'image-description' . ($block->isDescriptionInside() ? ' image-description--inside' : ''),
                'data-attribute' => 'image-description',
            ]);
        }

        $options = [
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
        ];
        return Html::tag('div', implode("\n", $contentBlock), array_merge($options, $block->getBlockAttributes()));
    }
}
