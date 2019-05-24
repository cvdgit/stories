<?php


namespace backend\components\story\reader;


use backend\components\story\ImageBlock;
use backend\components\story\layouts\OneColumnLayout;
use backend\components\story\layouts\TwoColumnLayout;
use backend\components\story\Slide;
use backend\components\story\Story;
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
        $slidesCount = count($slides);
        foreach ($slides as $i => $slide) {

            if ($i === 0 || $i === $slidesCount - 1) {
                $layout = new OneColumnLayout();
            }
            else {
                $layout = new TwoColumnLayout();
            }

            $this->loadSlide($slide, $layout);
        }
    }

    protected function loadSlide(AbstractSlide $powerPointSlide, $layout): void
    {
        $slide = $this->story->createSlide();
        $slide->setLayout($layout);

        $shapes = $powerPointSlide->getShapeCollection();
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

        $blocks = $slide->getBlocks();

        /** @var ImageBlock $block */
        $block = $blocks[0];

        $imagePath = $this->relativeImagesFolder . '/' . $powerPointShape->getIndexedFilename();
        $block->setFilePath($imagePath);
        $block->setImageSize($shapeImageFilePath, $powerPointShape->getWidth(), $powerPointShape->getHeight());
        $block->setNaturalImageSizeFromFile($shapeImageFilePath);
    }

    protected function loadShapeText(RichText $powerPointShape, Slide $slide): void
    {

        $blocks = $slide->getBlocks();
        if (get_class($slide->getLayout()) === OneColumnLayout::class) {
            $block = $blocks[0];
        }
        else {
            $block = $blocks[1];
        }

        $paragraphText = [];
        foreach ($powerPointShape->getParagraphs() as $paragraph) {
            $text = $paragraph->getPlainText();
            if ($text !== '') {
                $paragraphText[] = $text;
            }
        }

        $block->setText(implode('<br>', $paragraphText));
    }

}