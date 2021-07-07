<?php

namespace backend\components;

use backend\components\story\AbstractBlock;
use backend\components\story\BlockType;
use backend\components\story\HTMLBLock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\Slide;
use backend\components\story\TestBlockContent;
use backend\components\story\VideoBlock;
use backend\components\story\writer\HTMLWriter;
use backend\models\video\VideoSource;
use common\models\SlideVideo;
use common\models\StorySlideImage;
use common\models\StoryTest;

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

    public function addDescription(): self
    {
        foreach ($this->slide->getBlocks() as $block) {
            if (BlockType::isHtml($block)) {
                /** @var HTMLBLock $block */
                $content = TestBlockContent::createFromHtml($block->getContent());
                $testModel = StoryTest::findModel($content->getTestID());
                $block->setContent($content->render([], $testModel->title));
            }
            if (BlockType::isVideo($block) || BlockType::isVideoFile($block)) {
                /** @var VideoBlock $block */
                $videoModel = null;
                if ($block->getSource() === VideoSource::YOUTUBE) {
                    $videoModel = SlideVideo::findModelByVideoID($block->getVideoId());
                }
                else {
                    $videoModel = SlideVideo::findModel(pathinfo($block->getVideoId(), PATHINFO_FILENAME));
                }
                if ($videoModel !== null) {
                    $block->setContent($videoModel->title);
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
