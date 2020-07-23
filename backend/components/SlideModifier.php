<?php

namespace backend\components;

use backend\components\story\AbstractBlock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\writer\HTMLWriter;

class SlideModifier
{
    public function addImageParams($slideData)
    {
        $reader = new HtmlSlideReader($slideData);
        $slide = $reader->load();
        foreach ($slide->getBlocks() as $block) {
            if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
                /** @var $block ImageBlock */
                $delimiter = '?';
                if (strpos($block->getFilePath(), $delimiter) !== false) {
                    $delimiter = '&';
                }
                $block->setFilePath($block->getFilePath() . $delimiter . 't=' . time());
            }
        }
        $writer = new HTMLWriter();
        return $writer->renderSlide($slide);
    }

}