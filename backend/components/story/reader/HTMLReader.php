<?php


namespace backend\components\story\reader;


use backend\components\story\AbstractBlock;
use backend\components\story\ButtonBlock;
use backend\components\story\layouts\OneColumnLayout;
use backend\components\story\layouts\TwoColumnLayout;
use backend\components\story\Slide;
use backend\components\story\Story;
use backend\components\story\TransitionBlock;

class HTMLReader extends AbstractReader implements ReaderInterface
{

    protected $html;
    protected $rootPath;

    public function __construct($html)
    {
        $this->story = new Story();
        $this->html = $html;
    }

    public function load(): Story
    {
        $document = \phpQuery::newDocumentHTML($this->html);
        $this->loadSlides($document);
        return $this->story;
    }

    protected function loadSlides($document): void
    {
        $sections = $document->find('section');
        $slidesCount = count($sections);
        foreach ($sections as $i => $section) {

            if ($i === 0 || $i === $slidesCount - 1) {
                $layout = new OneColumnLayout();
            }
            else {
                $layout = new TwoColumnLayout();
            }

            $htmlSlide = pq($section)->htmlOuter();
            $this->loadSlide($htmlSlide, $layout);
        }
    }

    protected function loadSlide($htmlSlide, $layout): void
    {
        $slide = $this->story->createSlide();
        $slide->setLayout($layout);

        $blocks = pq($htmlSlide)->find('div.sl-block');
        $this->loadSlideBlocks($blocks, $slide);
    }

    protected function loadSlideBlocks($htmlBlocks, $slide)
    {
        foreach ($htmlBlocks as $htmlBlock) {
            $blockType = pq($htmlBlock)->attr('data-block-type');
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
                default:
            }
        }
    }

    private function styleToArray($style): array
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

    private function getStyleValue($style, $param): string
    {
        $value = '';
        if (!empty($style)) {
            $styleArray = $this->styleToArray($style);
            $value = $styleArray[$param] ?? '';
        }
        return $value;
    }

    protected function loadBlockImage($htmlBlock, Slide $slide): void
    {
        $blocks = $slide->getBlocks();
        $block = $blocks[0];

        $element = pq($htmlBlock)->find('img');
        $block->setFilePath($element->attr('data-src'));
        $block->setId(pq($htmlBlock)->attr('data-block-id'));

        $style = pq($htmlBlock)->attr('style');
        $width = str_replace('px', '', $this->getStyleValue($style, 'width'));
        $height = str_replace('px', '', $this->getStyleValue($style, 'height'));
        $block->setImageSize($element->attr('data-src'), $width, $height);
        $block->setNaturalImageSize($element->attr('data-natural-width'), $element->attr('data-natural-height'));

        $this->loadBlockProperties($block, $style);
    }

    protected function loadBlockText($htmlBlock, Slide $slide): void
    {
        $blocks = $slide->getBlocks();
        if (get_class($slide->getLayout()) === OneColumnLayout::class) {
            $block = $blocks[0];
            $style = pq($htmlBlock)->find('h1')->attr('style');
            $block->setFontSize($this->getStyleValue($style, 'font-size'));
            $block->setText(pq($htmlBlock)->find('h1')->html());
            $block->setId(pq($htmlBlock)->attr('data-block-id'));
        }
        else {
            $block = $blocks[1];
            $style = pq($htmlBlock)->find('p')->attr('style');
            $block->setFontSize($this->getStyleValue($style, 'font-size'));
            $block->setText(pq($htmlBlock)->find('p')->html());
            $block->setId(pq($htmlBlock)->attr('data-block-id'));
        }
        $this->loadBlockProperties($block, pq($htmlBlock)->attr('style'));
    }

    protected function loadBlockButton($htmlBlock, Slide $slide): void
    {
        $buttonBlock = new ButtonBlock();

        $style = pq($htmlBlock)->attr('style');
        $this->loadBlockProperties($buttonBlock, $style);

        $buttonBlock->setText(pq($htmlBlock)->find('a')->html());
        $buttonBlock->setId(pq($htmlBlock)->attr('data-block-id'));

        $style = pq($htmlBlock)->find('a')->attr('style');
        $buttonBlock->setFontSize($this->getStyleValue($style, 'font-size'));
        $buttonBlock->setUrl(pq($htmlBlock)->find('a')->attr('href'));
        $slide->addBlock($buttonBlock);
    }

    protected function loadBlockTransition($htmlBlock, Slide $slide): void
    {
        $block = new TransitionBlock();

        $element = pq($htmlBlock);

        $this->loadBlockProperties($block, $element->attr('style'));

        $block->setText(pq($htmlBlock)->find('button')->html());
        $block->setId(pq($htmlBlock)->attr('data-block-id'));

        $style = pq($htmlBlock)->find('button')->attr('style');
        $block->setFontSize($this->getStyleValue($style, 'font-size'));
        $block->setTransitionStoryId(pq($htmlBlock)->find('button')->attr('data-story-id'));
        $block->setSlides(pq($htmlBlock)->find('button')->attr('data-slides'));
        $slide->addBlock($block);
    }

    protected function loadBlockProperties(AbstractBlock $block, $style)
    {
        $block->setWidth($this->getStyleValue($style, 'width'));
        $block->setHeight($this->getStyleValue($style, 'height'));
        $block->setTop($this->getStyleValue($style, 'top'));
        $block->setLeft($this->getStyleValue($style, 'left'));
    }

}