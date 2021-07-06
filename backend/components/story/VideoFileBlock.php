<?php

namespace backend\components\story;

use backend\models\video\VideoSource;

class VideoFileBlock extends VideoBlock
{

    protected $type = AbstractBlock::TYPE_VIDEOFILE;

    public function create()
    {
        $block = new self();
        $block->setWidth('973px');
        $block->setHeight('720px');
        $block->setLeft(0);
        $block->setTop(0);
        $block->setDuration(0);
        $block->setSource(VideoSource::FILE);
        return $block;
    }
}
