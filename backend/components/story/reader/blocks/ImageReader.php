<?php

namespace backend\components\story\reader\blocks;

use backend\components\story\AbstractBlock;
use backend\components\story\ImageBlock;

class ImageReader extends AbstractBlockReader implements BlockReaderInterface
{

    public function createBlock(): AbstractBlock
    {
        $block = new ImageBlock();
        $block->setType(AbstractBlock::TYPE_IMAGE);
        $block->setId($this->pqBlock->attr('data-block-id'));

        $element = $this->pqBlock->find('img');
        $block->setFilePath($element->attr('data-src'));
        $block->setAction($element->attr('data-action'));
        $block->setActionStoryID($element->attr('data-action-story'));
        $block->setActionSlideID($element->attr('data-action-slide'));
        $block->setBackToNextSlide($element->attr('data-backtonextslide'));

        $style = $this->pqBlock->attr('style');
        $width = str_replace('px', '', $this->getStyleValue($style, 'width'));
        $height = str_replace('px', '', $this->getStyleValue($style, 'height'));
        $block->setImageSize($element->attr('data-src'), $width, $height);
        $block->setNaturalImageSize($element->attr('data-natural-width'), $element->attr('data-natural-height'));

        $imageSourceElement = $this->pqBlock->find('span');
        if ($imageSourceElement->length > 0) {
            $block->setImageSource($imageSourceElement->text());
        }

        $this->loadBlockProperties($block, $style);
        return $block;
    }
}