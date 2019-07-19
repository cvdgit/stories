<?php


namespace backend\components\story\reader;


use backend\components\story\AbstractBlock;
use backend\components\story\ImageBlock;
use backend\components\story\Slide;
use backend\components\story\Story;
use backend\components\story\TextBlock;
use Exception;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Drawing\Gd;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Slide\AbstractSlide;
use RuntimeException;

class PowerPointReader extends AbstractReader implements ReaderInterface
{

    protected $fileName;
    protected $imagesFolder;
    protected $relativeImagesFolder;

    protected $reader;

    protected $currentSlideNumber = 0;
    protected $currentSlideBlockNumber = 0;

    public function __construct($fileName, $imagesFolder, $relativeImagesFolder)
    {
        $this->story = new Story();
        $this->fileName = $fileName;
        $this->imagesFolder = $imagesFolder;
        $this->relativeImagesFolder = $relativeImagesFolder;
        $this->reader = IOFactory::createReader('PowerPoint2007');
    }

    private function clearImagesFolder(): void
    {
        if (!file_exists($this->imagesFolder)) {
            if (!mkdir($concurrentDirectory = $this->imagesFolder) && !is_dir($concurrentDirectory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }
        else {
            array_map('unlink', glob($this->imagesFolder . '*.*'));
        }
    }

    public function load(): Story
    {
        $this->clearImagesFolder();
        try {
            $presentation = $this->reader->load($this->fileName);
        } catch (Exception $e) {
            throw new RuntimeException('PhpPresentation file load exception - ' . $e->getMessage());
        }
        $this->loadSlides($presentation);
        return $this->story;
    }

    protected function loadSlides(PhpPresentation $presentation): void
    {
        $slides = $presentation->getAllSlides();
        $slideNumber = 1;
        foreach ($slides as $slide) {
            $this->loadSlide($slide, $slideNumber);
            $this->currentSlideNumber++;
            $slideNumber++;
        }
    }

    protected function loadSlide(AbstractSlide $powerPointSlide, $slideNumber): void
    {
        $slide = $this->story->createSlide();
        $slide->setSlideNumber($slideNumber);
        $shapes = $powerPointSlide->getShapeCollection();
        $this->currentSlideBlockNumber = count($shapes);
        $this->loadSlideShapes($shapes, $slide);
    }

    protected function loadSlideShapes($powerPointShapes, $slide): void
    {
        foreach ($powerPointShapes as $powerPointShape) {
            $className = get_class($powerPointShape);
            switch ($className) {
                case Gd::class:
                    $this->loadShapeImage($powerPointShape, $slide);
                    break;
                case RichText::class:
                    $this->loadShapeText($powerPointShape, $slide);
                default:
            }
        }
    }

    protected function loadShapeImage(Gd $powerPointShape, Slide $slide): void
    {
        $shapeImageFilePath = $this->imagesFolder . '/' . $powerPointShape->getIndexedFilename();
        file_put_contents($shapeImageFilePath, $powerPointShape->getContents());

        $block = new ImageBlock();
        $block->setType(AbstractBlock::TYPE_IMAGE);
        $block->setWidth('973px');
        $block->setHeight('720px');
        $block->setLeft(0);
        $block->setTop(0);

        $imagePath = $this->relativeImagesFolder . '/' . $powerPointShape->getIndexedFilename();
        $block->setFilePath($imagePath);
        $block->setImageSize($shapeImageFilePath, $powerPointShape->getWidth(), $powerPointShape->getHeight());
        $block->setNaturalImageSizeFromFile($shapeImageFilePath);

        $slide->addBlock($block);
    }

    protected function loadShapeText(RichText $powerPointShape, Slide $slide): void
    {
        $block = new TextBlock();
        if ($this->currentSlideBlockNumber === 1) {
            $block->setType(AbstractBlock::TYPE_HEADER);
            $block->setWidth('1200px');
            $block->setHeight('auto');
            $block->setLeft('14px');
            $block->setTop('294px');
            $block->setFontSize('3em');
        }
        else {
            $block->setType(AbstractBlock::TYPE_TEXT);
            $block->setWidth('290px');
            $block->setHeight('auto');
            $block->setLeft('983px');
            $block->setTop('9px');
            $block->setFontSize('0.8em');
        }

        $paragraphText = [];
        foreach ($powerPointShape->getParagraphs() as $paragraph) {
            $text = $paragraph->getPlainText();
            if ($text !== '') {
                $paragraphText[] = $text;
            }
        }

        $block->setText(implode('<br>', $paragraphText));
        $slide->addBlock($block);
    }

}