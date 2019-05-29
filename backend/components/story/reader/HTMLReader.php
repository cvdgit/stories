<?php


namespace backend\components\story\reader;


use backend\components\story\AbstractBlock;
use backend\components\story\ButtonBlock;
use backend\components\story\layouts\OneColumnLayout;
use backend\components\story\layouts\TwoColumnLayout;
use backend\components\story\Slide;
use backend\components\story\Story;

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
                case 'text':
                    $this->loadBlockText($htmlBlock, $slide);
                    break;
                case 'image':
                    $this->loadBlockImage($htmlBlock, $slide);
                    break;
                case 'button':
                    $this->loadBlockButton($htmlBlock, $slide);
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
    }

    protected function loadBlockButton($htmlBlock, Slide $slide): void
    {
        $buttonBlock = new ButtonBlock();

        $style = pq($htmlBlock)->attr('style');
        $buttonBlock->setWidth($this->getStyleValue($style, 'width'));
        $buttonBlock->setHeight($this->getStyleValue($style, 'height'));
        $buttonBlock->setTop($this->getStyleValue($style, 'top'));
        $buttonBlock->setLeft($this->getStyleValue($style, 'left'));

        $buttonBlock->setText(pq($htmlBlock)->find('a')->html());
        $buttonBlock->setId(pq($htmlBlock)->attr('data-block-id'));

        $style = pq($htmlBlock)->find('a')->attr('style');
        $buttonBlock->setFontSize($this->getStyleValue($style, 'font-size'));
        $buttonBlock->setUrl(pq($htmlBlock)->find('a')->attr('href'));
        $slide->addBlock($buttonBlock);
    }

}