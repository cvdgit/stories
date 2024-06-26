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
        $block->setBlockAttributes($this->pqBlock->attr('*'));

        $style = $this->pqBlock->attr('style');
        $element = $this->pqBlock->find('img');
        if ($element->length > 0) {

            $filePath = $element->attr('data-src');
            if ($filePath === null) {
                $filePath = $element->attr('src');
            }

            $block->setElementAttributes($element->attr('*'));
            $block->setFilePath($filePath);
            $block->setAction($element->attr('data-action'));
            $block->setActionStoryID($element->attr('data-action-story'));
            $block->setActionSlideID($element->attr('data-action-slide'));
            $block->setBackToNextSlide($element->attr('data-backtonextslide'));

            $width = str_replace('px', '', $this->getStyleValue($style, 'width'));
            $height = str_replace('px', '', $this->getStyleValue($style, 'height'));
            $block->setImageSize($filePath, $width, $height);
            $block->setNaturalImageSize($element->attr('data-natural-width'), $element->attr('data-natural-height'));

            $imageSourceElement = $this->pqBlock->find('span');
            if ($imageSourceElement->length > 0) {
                $block->setImageSource($imageSourceElement->text());
            }

            $descriptionElement = $this->pqBlock->find('div.image-description');
            if ($descriptionElement->length > 0) {
                $block->setDescription($descriptionElement->text());
                $block->setDescriptionInside($descriptionElement->hasClass('image-description--inside'));
            }
        }

        $this->loadBlockProperties($block, $style);
        return $block;
    }
}
