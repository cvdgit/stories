<?php

namespace backend\components\story\reader\blocks;

use backend\components\story\AbstractBlock;
use backend\components\story\VideoBlock;

class VideoReader extends AbstractBlockReader implements BlockReaderInterface
{

    public function createBlock(): AbstractBlock
    {
        $block = new VideoBlock();
        $block->setType(AbstractBlock::TYPE_VIDEO);
        $block->setId($this->pqBlock->attr('data-block-id'));

        $element = $this->pqBlock->find('div.wikids-video-player');
        $block->setVideoId($element->attr('data-video-id'));
        $block->setSeekTo($element->attr('data-seek-to'));
        $block->setDuration($element->attr('data-video-duration'));
        $block->setMute($element->attr('data-mute') === 'true' ? 1 : 0);
        $block->setToNextSlide($element->attr('data-to-next-slide') === 'true' ? 1 : 0);
        $block->setSource($element->attr('data-source'));
        $volume = $element->attr('data-volume');
        if (empty($volume)) {
            $volume = VideoBlock::DEFAULT_VOLUME;
        }
        $block->setVolume($volume);
        $speed = $element->attr('data-speed');
        if (empty($speed)) {
            $speed = VideoBlock::DEFAULT_SPEED;
        }
        $block->setSpeed($speed);

        $style = $this->pqBlock->attr('style');
        $this->loadBlockProperties($block, $style);
        return $block;
    }
}