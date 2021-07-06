<?php

namespace backend\components;

use backend\components\story\AbstractBlock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\Slide;
use backend\components\story\writer\HTMLWriter;
use common\models\StorySlideImage;

class SlideModifier
{

    /** @var Slide */
    private $slide;

    public function __construct(int $slideID, string $slideData)
    {
        $this->slide = (new HtmlSlideReader($slideData))->load();
        $this->slide->setId($slideID);
    }

    public function addImageParams(): self
    {
        foreach ($this->slide->getBlocks() as $block) {
            if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
                /** @var $block ImageBlock */
                $delimiter = '?';
                if (strpos($block->getFilePath(), $delimiter) !== false) {
                    $delimiter = '&';
                }
                $block->setFilePath($block->getFilePath() . $delimiter . 't=' . str_replace(' ', '', microtime()));
            }
        }
        return $this;
    }

    public function addImageId(): self
    {
        foreach ($this->slide->getBlocks() as $block) {
            if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
                /** @var $block ImageBlock */

                $path = $block->getFilePath();
                $image = null;
                if (strpos($path, '://') !== false) {
                    $query = parse_url($path, PHP_URL_QUERY);
                    parse_str($query, $result);
                    $imageHash = $result['id'];
                    try {
                        $image = StorySlideImage::findByHash($imageHash);
                    }
                    catch (\Exception $ex) {}
                }
                else {
                    try {
                        $image = StorySlideImage::findByPath(basename(dirname($path)), basename($path));
                    }
                    catch (\Exception $ex) {}
                }
                if ($image !== null) {
                    $block->setBlockAttribute('data-image-id', $image->id);
                }
            }
        }
        return $this;
    }

    public function render(): string
    {
        return (new HTMLWriter())->renderSlide($this->slide);
    }

}