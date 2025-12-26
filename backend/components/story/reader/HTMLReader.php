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
use backend\components\story\Story;
use backend\components\story\TestBlock;
use backend\components\story\TextBlock;
use backend\components\story\TransitionBlock;
use backend\components\story\VideoBlock;
use phpQuery;

class HTMLReader extends AbstractReader implements ReaderInterface
{
    private $html;
    /*private $rootPath;*/

    public function __construct(string $html)
    {
        $this->story = new Story();
        $this->html = $html;
    }

    public function load(): Story
    {
        $document = phpQuery::newDocumentHTML($this->html);
        $this->loadSlides($document);
        return $this->story;
    }

    private function loadSlides(\phpQueryObject $document): void
    {
        $sections = $document->find('section');
        $slideNumber = 1;
        foreach ($sections as $section) {
            $htmlSlide = pq($section)->htmlOuter();
            $this->loadSlide($htmlSlide, $slideNumber);
            $slideNumber++;
        }
    }

    private function loadSlide(string $htmlSlide, int $slideIndex): void
    {
        $slide = $this->story->createSlide();
        $slide->setContent($htmlSlide);
        $slide->setSlideNumber($slideIndex);
        $slide->setView(pq($htmlSlide)->attr('data-slide-view') ?? '');
        $slide->id = (int) pq($htmlSlide)->attr('data-id');
        $blocks = pq($htmlSlide)->find('div.sl-block');
        $this->loadSlideBlocks($blocks, $slide);
    }

    private function loadSlideBlocks(\phpQueryObject $htmlBlocks, Slide $slide): void
    {
        foreach ($htmlBlocks as $htmlBlock) {
            $blockType = (string) pq($htmlBlock)->attr('data-block-type');
            switch ($blockType) {
                case AbstractBlock::TYPE_TEXT:
                    $this->loadBlockText($htmlBlock, $slide);
                    break;
                case AbstractBlock::TYPE_IMAGE:
                    $this->loadBlockImage($htmlBlock, $slide);
                    break;
                case AbstractBlock::TYPE_BUTTON:
                    $this->loadBlockButton($htmlBlock, $slide);
                    break;
                case AbstractBlock::TYPE_TRANSITION:
                    $this->loadBlockTransition($htmlBlock, $slide);
                    break;
                case AbstractBlock::TYPE_TEST:
                    $this->loadBlockTest($htmlBlock, $slide);
                    break;
                case AbstractBlock::TYPE_HTML:
                    $this->loadBlockHtml($htmlBlock, $slide);
                    break;
                case AbstractBlock::TYPE_VIDEO:
                    $this->loadBlockVideo($htmlBlock, $slide);
                    break;
                case AbstractBlock::TYPE_MENTAL_MAP:
                    $this->loadBlockMentalMap($htmlBlock, $slide);
                    break;
                case AbstractBlock::TYPE_RETELLING:
                    $this->loadBlockRetelling($htmlBlock, $slide);
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

    private function loadBlockImage(\DOMElement $htmlBlock, Slide $slide): void
    {
        $block = new ImageBlock();
        $block->setType(AbstractBlock::TYPE_IMAGE);

        $element = pq($htmlBlock)->find('img');
        $block->setFilePath($element->attr('data-src'));
        $block->setId(pq($htmlBlock)->attr('data-block-id'));

        $style = pq($htmlBlock)->attr('style');
        $width = str_replace('px', '', $this->getStyleValue($style, 'width'));
        $height = str_replace('px', '', $this->getStyleValue($style, 'height'));
        $block->setImageSize($element->attr('data-src'), $width, $height);
        $block->setNaturalImageSize($element->attr('data-natural-width'), $element->attr('data-natural-height'));

        $this->loadBlockProperties($block, $style);

        $slide->addBlock($block);
    }

    protected function loadBlockText(\DOMElement $htmlBlock, Slide $slide): void
    {
        $block = new TextBlock();
        $block->setId(pq($htmlBlock)->attr('data-block-id'));
        if (pq($htmlBlock)->find('h1')->length > 0) {
            $block->setType(AbstractBlock::TYPE_HEADER);
            $style = pq($htmlBlock)->find('h1')->attr('style');
            $text = pq($htmlBlock)->find('h1')->html();
        }
        else {
            $block->setType(AbstractBlock::TYPE_TEXT);
            $style = pq($htmlBlock)->find('p')->attr('style');
            $text = pq($htmlBlock)->find('p')->html();
        }
        $block->setText($text);

        $this->loadBlockProperties($block, pq($htmlBlock)->attr('style'));

        $slide->addBlock($block);
    }

    protected function loadBlockButton($htmlBlock, Slide $slide): void
    {
        $buttonBlock = new ButtonBlock();
        $buttonBlock->setType(AbstractBlock::TYPE_BUTTON);

        $style = pq($htmlBlock)->attr('style');
        $this->loadBlockProperties($buttonBlock, $style);

        $buttonBlock->setText(pq($htmlBlock)->find('a')->html());
        $buttonBlock->setId(pq($htmlBlock)->attr('data-block-id'));

        $style = pq($htmlBlock)->find('a')->attr('style');
        $buttonBlock->setUrl(pq($htmlBlock)->find('a')->attr('href'));
        $slide->addBlock($buttonBlock);
    }

    protected function loadBlockTransition($htmlBlock, Slide $slide): void
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
        $slide->addBlock($block);
    }

    protected function loadBlockTest($htmlBlock, Slide $slide): void
    {
        $block = new TestBlock();
        $block->setType(AbstractBlock::TYPE_TEST);

        $element = pq($htmlBlock);

        $this->loadBlockProperties($block, $element->attr('style'));

        $block->setText(pq($htmlBlock)->find('button')->html());
        $block->setId(pq($htmlBlock)->attr('data-block-id'));

        $style = pq($htmlBlock)->find('button')->attr('style');
        $block->setTestID(pq($htmlBlock)->find('button')->attr('data-test-id'));
        $slide->addBlock($block);
    }

    private function loadBlockHtml(\DOMElement $htmlBlock, Slide $slide): void
    {
        $block = new HtmlBlock();
        $block->setType(AbstractBlock::TYPE_HTML);

        $element = pq($htmlBlock);

        $this->loadBlockProperties($block, $element->attr('style'));
        $block->setId(pq($htmlBlock)->attr('data-block-id'));
        $block->setContent(pq($htmlBlock)->html());

        $slide->addBlock($block);
    }

    private function loadBlockMentalMap(\DOMElement $htmlBlock, Slide $slide): void
    {
        $block = new MentalMapBlock();
        $block->setType(AbstractBlock::TYPE_MENTAL_MAP);

        $element = pq($htmlBlock);

        $this->loadBlockProperties($block, $element->attr('style'));
        $block->setId(pq($htmlBlock)->attr('data-block-id'));
        $block->setContent(pq($htmlBlock)->html());

        $slide->addBlock($block);
    }

    private function loadBlockRetelling(\DOMElement $htmlBlock, Slide $slide): void
    {
        $block = new RetellingBlock();
        $block->setType(AbstractBlock::TYPE_RETELLING);

        $element = pq($htmlBlock);

        $this->loadBlockProperties($block, $element->attr('style'));
        $block->setId(pq($htmlBlock)->attr('data-block-id'));
        $block->setContent(pq($htmlBlock)->html());

        $slide->addBlock($block);
    }

    protected function loadBlockVideo($htmlBlock, Slide $slide): void
    {
        $block = new VideoBlock();
        $block->setType(AbstractBlock::TYPE_VIDEO);
        $block->setId(pq($htmlBlock)->attr('data-block-id'));

        $element = pq($htmlBlock)->find('div.wikids-video-player');
        $block->setVideoId(pq($element)->attr('data-video-id'));
        $block->setSeekTo((float) pq($element)->attr('data-seek-to'));
        $block->setDuration((float) pq($element)->attr('data-video-duration'));
        $block->setMute(pq($element)->attr('data-mute') === 'true');
        $block->setVolume((float) pq($element)->attr('data-volume'));

        $style = pq($htmlBlock)->attr('style');
        $this->loadBlockProperties($block, $style);
        $slide->addBlock($block);
    }

    protected function loadBlockProperties(AbstractBlock $block, string $style): void
    {
        $block->setWidth($this->getStyleValue($style, 'width'));
        $block->setHeight($this->getStyleValue($style, 'height'));
        $block->setTop($this->getStyleValue($style, 'top'));
        $block->setLeft($this->getStyleValue($style, 'left'));
    }
}
