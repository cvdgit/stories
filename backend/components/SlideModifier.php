<?php

namespace backend\components;

use backend\components\story\AbstractBlock;
use backend\components\story\BlockType;
use backend\components\story\HTMLBLock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\Slide;
use backend\components\story\TestBlockContent;
use backend\components\story\TextBlock;
use backend\components\story\VideoBlock;
use backend\components\story\writer\HTMLWriter;
use backend\models\video\VideoSource;
use common\helpers\Url;
use common\models\SlideVideo;
use common\models\StorySlideImage;
use common\models\StoryTest;

class SlideModifier
{
    /** @var Slide */
    private $slide;

    public function __construct(int $slideID, string $slideData)
    {
        $search = [
            'data-id=""',
            'data-background-color="#000000"',
        ];
        $replace = [
            'data-id="' . $slideID . '"',
            'data-background-color="#fff"',
        ];
        $slideData = str_replace($search, $replace, $slideData);

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

    public function addImageUrl(): self
    {
        foreach ($this->slide->getBlocks() as $block) {
            if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
                /** @var $block ImageBlock */
                $path = $block->getFilePath();
                if (strpos($path, '://') === false) {
                    $path = Url::homeUrl() . $path;
                }
                $block->setFilePath($path);
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
                if (empty($path)) {
                    continue;
                }
                if (($image = StorySlideImage::findImageByPath($path)) !== null) {
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
                try {
                    $testModel = StoryTest::findModel($content->getTestID());
                    $block->setContent($content->render([], $testModel->title));
                }
                catch (\Exception $ex) {
                    $block->setContent($content->render([], $ex->getMessage()));
                }
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

    public function addVideoUrl(): self
    {
        foreach ($this->slide->getBlocks() as $block) {
            if (($block->getType() === AbstractBlock::TYPE_VIDEO || $block->getType() === AbstractBlock::TYPE_VIDEOFILE)) {
                /** @var $block VideoBlock */
                if ($block->getSource() === VideoSource::FILE) {
                    $path = $block->getVideoId();
                    if (empty($path)) {
                        continue;
                    }
                    $block->setVideoId('https://wikids.ru' . $path);
                }
            }
        }
        return $this;
    }

    private function processTextContent(string $content): array
    {
        $links = [];
        $matches = [];
        $result = preg_match_all('~<a [^<>]*href=[\'"]([^\'"]+)[\'"][^<>]*>([^</a>]*)~i', $content, $matches, PREG_SET_ORDER);
        if ($result !== false && $result > 0) {
            $site = Url::homeUrl();
            $site = preg_replace('/https?:\/\//', '', $site);
            foreach ($matches as $match) {
                $url = $match[1];
                $urlMatches = [];
                $urlMatchResult = preg_match("~$site/story/([a-z0-9\-/]+)#/(\d+)~i", $url, $urlMatches);
                if ($urlMatchResult !== false && $urlMatchResult > 0) {
                    $links[] = [
                        'url' => $url,
                        'alias' => $urlMatches[1],
                        'number' => $urlMatches[2],
                    ];
                }
            }
        }
        return [
            'paragraph' => $content,
            'links' => $links,
        ];
    }

    public function forLesson(): array
    {
        $textBlocks = [];
        $imageBlocks = [];
        $quizBlocks = [];
        $videoBlocks = [];
        foreach ($this->slide->getBlocks() as $slideBlock) {
            if (BlockType::isText($slideBlock)) {
                /** @var $slideBlock TextBlock */
                $textBlocks[] = $slideBlock;
            }
            if (BlockType::isImage($slideBlock)) {
                /** @var $slideBlock ImageBlock */
                $imageBlocks[] = $slideBlock;
            }
            if (BlockType::isHtml($slideBlock)) {
                /** @var $slideBlock HTMLBLock */
                $quizBlocks[] = $slideBlock;
            }
            if (BlockType::isVideo($slideBlock) || BlockType::isVideoFile($slideBlock)) {
                /** @var $slideBlock VideoBlock */
                $videoBlocks[] = $slideBlock;
            }
        }

        $blocks = [];
        $links = [];
        if (count($textBlocks) === 1 && count($imageBlocks) === 1) {
            $textBlock = $textBlocks[0];
            $imageBlock = $imageBlocks[0];
            $textContent = $this->processTextContent(trim($textBlock->getText()));
            $links = array_merge($links, $textContent['links']);
            $items = [
                [
                    'id' => $imageBlock->getId(),
                    'image' => [
                        'url' => $imageBlock->getFilePath(),
                    ],
                    'caption' => '',
                    'paragraph' => $textContent['paragraph'],
                ],
            ];
            $block = [
                'id' => $imageBlock->getId(),
                'type' => 'image',
                'items' => $items,
                'layout' => 'text-aside',
                'settings' => [
                    'imagePosition' => 'right',
                ],
            ];
            $blocks[] = $block;
        }
        else {

            if (count($quizBlocks) > 0) {
                $content = TestBlockContent::createFromHtml($quizBlocks[0]->getContent());
                $testModel = StoryTest::findModel($content->getTestID());
                $block = [
                    'id' => $content->getTestID(),
                    'title' => $testModel->header,
                    'description' => $testModel->description_text,
                ];
                $blocks[] = $block;
            }
            else if (count($videoBlocks) > 0) {
                $videoBlock = $videoBlocks[0];
                $block = [
                    'id' => $videoBlock->getId(),
                    'type' => 'multimedia',
                    'items' => [
                        [
                            'video' => [
                                'url' => $videoBlock->getVideoId(),
                                'video_id' => $videoBlock->getVideoId(),
                                'seek_to' => (double)$videoBlock->getSeekTo(),
                                'duration' => (int)$videoBlock->getDuration(),
                                'mute' => (int)$videoBlock->isMute() === 1,
                                'speed' => (int)$videoBlock->getSpeed(),
                                'volume' => (double)$videoBlock->getVolume(),
                                'source' => $videoBlock->getSource(),
                            ],
                        ],
                    ],
                    'layout' => 'video',
                ];
                $blocks[] = $block;
            }
            else {

                foreach ($this->slide->getBlocks() as $slideBlock) {
                    $block = null;
                    if (BlockType::isText($slideBlock)) {
                        /** @var $slideBlock TextBlock */
                        $textContent = $this->processTextContent(trim($slideBlock->getText()));
                        $links = array_merge($links, $textContent['links']);
                        $items = [
                            [
                                'id' => $slideBlock->getId(),
                                'paragraph' => $textContent['paragraph'],
                            ],
                        ];
                        $block = [
                            'id' => $slideBlock->getId(),
                            'type' => 'text',
                            'items' => $items,
                        ];
                    }
                    if (BlockType::isImage($slideBlock)) {
                        /** @var $slideBlock ImageBlock */
                        $items = [
                            [
                                'id' => $slideBlock->getId(),
                                'image' => [
                                    'url' => $slideBlock->getFilePath(),
                                ],
                                'layout' => 'image',
                                'caption' => '',
                                'paragraph' => '',
                            ],
                        ];
                        $block = [
                            'id' => $slideBlock->getId(),
                            'type' => 'image',
                            'items' => $items,
                        ];
                    }
                    if ($block !== null) {
                        $blocks[] = $block;
                    }
                }
            }
        }

        return [
            'blocks' => $blocks,
            'links' => $links,
        ];
    }
}
