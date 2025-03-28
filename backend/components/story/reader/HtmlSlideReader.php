<?php

declare(strict_types=1);

namespace backend\components\story\reader;

use backend\components\story\AbstractBlock;
use backend\components\story\ButtonBlock;
use backend\components\story\HTMLBLock;
use backend\components\story\ImageBlock;
use backend\components\story\MentalMapBlock;
use backend\components\story\RetellingBlock;
use backend\components\story\Slide;
use backend\components\story\TestBlock;
use backend\components\story\TextBlock;
use backend\components\story\TransitionBlock;
use backend\components\story\VideoBlock;
use backend\models\video\VideoSource;

class HtmlSlideReader implements ReaderInterface
{
    private $slide;
    private $html;

    public function __construct(string $html)
    {
        $this->slide = new Slide();
        $this->html = $html;
    }

    public function load(): Slide
    {
        $htmlSlide = \phpQuery::newDocument($this->html, 'text/html');
        $this->loadSlide($htmlSlide);
        return $this->slide;
    }

    private function loadSlide(\phpQueryObject $htmlSlide): void
    {
        $this->slide->setView(pq($htmlSlide)->find('section')->attr('data-slide-view') ?? '');
        $this->slide->setId((int) pq($htmlSlide)->find('section')->attr('data-id'));
        $blocks = pq($htmlSlide)->find('div.sl-block');
        $this->loadSlideBlocks($blocks);
    }

    private function loadSlideBlocks(\phpQueryObject $htmlBlocks): void
    {
        foreach ($htmlBlocks as $htmlBlock) {
            $blockType = (string) pq($htmlBlock)->attr('data-block-type');
            switch ($blockType) {
                case AbstractBlock::TYPE_TEXT:
                    $this->loadBlockText($htmlBlock);
                    break;
                case AbstractBlock::TYPE_IMAGE:
                    $this->loadBlockImage($htmlBlock);
                    break;
                case AbstractBlock::TYPE_BUTTON:
                    $this->loadBlockButton($htmlBlock);
                    break;
                case AbstractBlock::TYPE_TRANSITION:
                    $this->loadBlockTransition($htmlBlock);
                    break;
                case AbstractBlock::TYPE_TEST:
                    $this->loadBlockTest($htmlBlock);
                    break;
                case AbstractBlock::TYPE_HTML:
                    $this->loadBlockHtml($htmlBlock);
                    break;
                case AbstractBlock::TYPE_MENTAL_MAP:
                    $this->loadBlockMentalMap($htmlBlock);
                    break;
                case AbstractBlock::TYPE_RETELLING:
                    $this->loadBlockRetelling($htmlBlock);
                    break;
                case AbstractBlock::TYPE_VIDEO:
                case AbstractBlock::TYPE_VIDEOFILE:
                    $this->loadBlockVideo($htmlBlock);
                    break;
                default:
            }
        }
    }

    private function styleToArray(string $style): array
    {
        $styleArray = [];
        foreach (explode(';', $style) as $part) {
            if (!empty($part)) {
                [$paramName, $paramValue] = explode(':', $part);
                $styleArray[trim($paramName)] = trim($paramValue);
            }
        }
        return $styleArray;
    }

    private function getStyleValue(string $style, string $param): string
    {
        $value = '';
        if (!empty($style)) {
            $styleArray = $this->styleToArray($style);
            $value = $styleArray[$param] ?? '';
        }
        return $value;
    }

    protected function loadBlockImage($htmlBlock): void
    {
        $block = new ImageBlock();
        $block->setType(AbstractBlock::TYPE_IMAGE);
        $block->setId(pq($htmlBlock)->attr('data-block-id'));
        $block->setBlockAttributes(pq($htmlBlock)->attr('*'));

        $element = pq($htmlBlock)->find('img');
        $style = pq($htmlBlock)->attr('style');
        if ($element->length > 0) {

            $block->setElementAttributes($element->attr('*'));
            $block->setFilePath($element->attr('data-src'));
            $block->setAction($element->attr('data-action'));
            $block->setActionStoryID($element->attr('data-action-story'));
            $block->setActionSlideID($element->attr('data-action-slide'));
            $block->setBackToNextSlide($element->attr('data-backtonextslide'));
            $block->setNaturalImageSize($element->attr('data-natural-width'), $element->attr('data-natural-height'));

            $width = str_replace('px', '', $this->getStyleValue($style, 'width'));
            $height = str_replace('px', '', $this->getStyleValue($style, 'height'));
            $block->setImageSize($element->attr('data-src'), $width, $height);

            $imageSourceElement = pq($htmlBlock)->find('span');
            if ($imageSourceElement->length > 0) {
                $block->setImageSource($imageSourceElement->text());
            }

            $descriptionElement = pq($htmlBlock)->find('div.image-description');
            if ($descriptionElement->length > 0) {
                $block->setDescription($descriptionElement->text());
                $block->setDescriptionInside($descriptionElement->hasClass('image-description--inside'));
            }
        }

        $this->loadBlockProperties($block, $style);

        $this->slide->addBlock($block);
    }

    protected function loadBlockText(\DOMElement $htmlBlock): void
    {
        $block = new TextBlock();
        $block->setType(AbstractBlock::TYPE_TEXT);
        $block->setId(pq($htmlBlock)->attr('data-block-id'));
        if (pq($htmlBlock)->find('h1')->length > 0) {
            $style = pq($htmlBlock)->find('h1')->attr('style');
            $text = pq($htmlBlock)->find('h1')->html();
            if (empty($text)) {
                $text = pq($htmlBlock)->find('p')->text();
            }
            $text = '<p>' . $text . '</p>';
        } else {
            $selector = 'p';
            if (pq($htmlBlock)->find('div.slide-paragraph')->length > 0) {
                $selector = 'div.slide-paragraph';
                $text = pq($htmlBlock)->find($selector)->html();
            } else {
                $text = '<p>' . pq($htmlBlock)->find($selector)->html() . '</p>';
            }
            $style = pq($htmlBlock)->find($selector)->attr('style');
        }

        $block->setText($text);

        $this->loadBlockProperties($block, pq($htmlBlock)->attr('style'));

        $this->slide->addBlock($block);
    }

    protected function loadBlockButton($htmlBlock): void
    {
        $buttonBlock = new ButtonBlock();
        $buttonBlock->setType(AbstractBlock::TYPE_BUTTON);

        $style = pq($htmlBlock)->attr('style');
        $this->loadBlockProperties($buttonBlock, $style);

        $buttonBlock->setText(pq($htmlBlock)->find('a')->html());
        $buttonBlock->setId(pq($htmlBlock)->attr('data-block-id'));

        $style = pq($htmlBlock)->find('a')->attr('style');
        $buttonBlock->setUrl(pq($htmlBlock)->find('a')->attr('href'));
        $this->slide->addBlock($buttonBlock);
    }

    protected function loadBlockTransition($htmlBlock): void
    {
        $block = new TransitionBlock();
        $block->setType(AbstractBlock::TYPE_TRANSITION);

        $element = pq($htmlBlock);

        $this->loadBlockProperties($block, $element->attr('style'));

        $block->setText(pq($htmlBlock)->find('button')->html());
        $block->setId(pq($htmlBlock)->attr('data-block-id'));

        $style = pq($htmlBlock)->find('button')->attr('style');
        $block->setTransitionStoryId(pq($htmlBlock)->find('button')->attr('data-story-id'));
        $block->setSlides(pq($htmlBlock)->find('button')->attr('data-slides'));
        $block->setBackToNextSlide(pq($htmlBlock)->find('button')->attr('data-backtonextslide'));
        $this->slide->addBlock($block);
    }

    protected function loadBlockTest($htmlBlock): void
    {
        $block = new TestBlock();
        $block->setType(AbstractBlock::TYPE_TEST);

        $element = pq($htmlBlock);

        $this->loadBlockProperties($block, $element->attr('style'));

        $block->setText(pq($htmlBlock)->find('button')->html());
        $block->setId(pq($htmlBlock)->attr('data-block-id'));

        $style = pq($htmlBlock)->find('button')->attr('style');
        $block->setTestId(pq($htmlBlock)->find('button')->attr('data-test-id'));
        $this->slide->addBlock($block);
    }

    private function loadBlockHtml(\DOMElement $htmlBlock): void
    {
        $block = new HtmlBlock();
        $block->setType(AbstractBlock::TYPE_HTML);
        $element = pq($htmlBlock);
        $this->loadBlockProperties($block, $element->attr('style'));
        $block->setId(pq($htmlBlock)->attr('data-block-id'));
        $block->setContent(pq($htmlBlock)->find('.sl-block-content:eq(0)')->html());
        $this->slide->addBlock($block);
    }

    private function loadBlockMentalMap(\DOMElement $htmlBlock): void
    {
        $block = new MentalMapBlock();
        $block->setType(AbstractBlock::TYPE_MENTAL_MAP);
        $element = pq($htmlBlock);
        $this->loadBlockProperties($block, (string) $element->attr('style'));
        $block->setId((string) pq($htmlBlock)->attr('data-block-id'));
        $block->setContent((string) pq($htmlBlock)->find('.sl-block-content:eq(0)')->html());
        $this->slide->addBlock($block);
    }

    private function loadBlockRetelling(\DOMElement $htmlBlock): void
    {
        $block = new RetellingBlock();
        $block->setType(AbstractBlock::TYPE_RETELLING);
        $element = pq($htmlBlock);
        $this->loadBlockProperties($block, (string) $element->attr('style'));
        $block->setId((string) pq($htmlBlock)->attr('data-block-id'));
        $block->setContent((string) pq($htmlBlock)->find('.sl-block-content:eq(0)')->html());
        $this->slide->addBlock($block);
    }

    protected function loadBlockVideo($htmlBlock): void
    {
        $block = new VideoBlock();
        $block->setType(AbstractBlock::TYPE_VIDEO);
        $block->setId(pq($htmlBlock)->attr('data-block-id'));

        $element = pq($htmlBlock)->find('div.wikids-video-player');
        $block->setVideoId(pq($element)->attr('data-video-id'));
        $block->setSeekTo((float) pq($element)->attr('data-seek-to'));
        $block->setDuration((float) pq($element)->attr('data-video-duration'));
        $block->setMute(pq($element)->attr('data-mute') === 'true');
        $block->setToNextSlide(pq($element)->attr('data-to-next-slide') === 'true');
        $block->setShowCaptions(pq($element)->attr('data-show-captions') === 'true');
        $block->setCaptionsUrl(pq($element)->attr('data-captions-url'));

        $videoSource = pq($element)->attr('data-source');
        if (empty($videoSource)) {
            $videoSource = VideoSource::YOUTUBE;
        }
        $block->setSource((int) $videoSource);

        $volume = pq($element)->attr('data-volume');
        if (empty($volume)) {
            $volume = VideoBlock::DEFAULT_VOLUME;
        }
        $block->setVolume((float) $volume);
        $speed = pq($element)->attr('data-speed');
        if (empty($speed)) {
            $speed = VideoBlock::DEFAULT_SPEED;
        }
        $block->setSpeed((float) $speed);

        $style = pq($htmlBlock)->attr('style');
        $this->loadBlockProperties($block, $style);
        $this->slide->addBlock($block);
    }

    protected function loadBlockProperties(AbstractBlock $block, $style)
    {
        $block->setWidth($this->getStyleValue($style, 'width'));
        $block->setHeight($this->getStyleValue($style, 'height'));
        $block->setTop($this->getStyleValue($style, 'top'));
        $block->setLeft($this->getStyleValue($style, 'left'));
    }
}
