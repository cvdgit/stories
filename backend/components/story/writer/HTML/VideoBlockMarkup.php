<?php

namespace backend\components\story\writer\HTML;

use backend\components\story\AbstractBlock;
use backend\components\story\VideoBlock;
use backend\components\story\writer\HTML\elements\VideoElement;
use yii\helpers\Html;

class VideoBlockMarkup extends AbstractMarkup
{

    public function __construct(AbstractBlock $block)
    {
        parent::__construct($block, new VideoElement());
    }

    private function getElementMarkup(VideoBlock $block): string
    {
        $element = $this->getElement();
        return Html::tag($element->getTagName(), $block->getContent(), [
            'class' => 'wikids-video-player',
            'data-video-id' => $block->getVideoId(),
            'data-seek-to' => $block->getSeekTo(),
            'data-video-duration' => $block->getDuration(),
            'data-mute' => var_export((bool)$block->getMute(), true),
            'data-to-next-slide' => var_export((bool)$block->getToNextSlide(), true),
            'data-speed' => $block->getSpeed(),
            'data-volume' => $block->getVolume(),
            'data-source' => $block->getSource(),
        ]);
    }

    public function markup(): string
    {
        /** @var VideoBlock $block */
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
            'data-block-type' => $block->getType(),
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
{

}