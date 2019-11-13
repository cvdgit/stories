<?php


namespace backend\components\story\reader;


use backend\components\story\AbstractBlock;
use backend\components\story\ImageBlock;
use backend\components\story\Slide;
use backend\components\story\Story;
use backend\components\story\TextBlock;
use Exception;
use yii\imagine\Image;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Drawing\Gd;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Slide\AbstractSlide;
use RuntimeException;

class PowerPointReader extends AbstractReader implements ReaderInterface
{

    protected $fileName;
    protected $rootImagesFolder;
    protected $imagesFolder;

    protected $reader;

    protected $slideNumber;
    protected $currentSlideNumber = 0;
    protected $currentSlideBlockNumber = 0;

    public function __construct($fileName, $rootImagesFolder, $imagesFolder)
    {
        $this->story = new Story();
        $this->fileName = $fileName;
        $this->rootImagesFolder = $rootImagesFolder;
        $this->imagesFolder = $imagesFolder;
        $this->reader = IOFactory::createReader('PowerPoint2007');
    }

    public function getImagesPath()
    {
        return $this->imagesFolder;
    }

    public function getRootImagesPath()
    {
        return $this->rootImagesFolder . $this->imagesFolder;
    }

    private function clearImagesFolder(): void
    {
        $folder = $this->getRootImagesPath();
        if (!file_exists($folder)) {
            if (!mkdir($concurrentDirectory = $folder) && !is_dir($concurrentDirectory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }
        else {
            array_map('unlink', glob($folder . DIRECTORY_SEPARATOR . '*.*'));
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
        $this->slideNumber = count($slides);
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
        $slide->setView(Slide::VIEW_SLIDE);
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
        $block = new ImageBlock();
        $block->setType(AbstractBlock::TYPE_IMAGE);
        $block->setWidth($powerPointShape->isResizeProportional() . 'px');
        $block->setHeight($powerPointShape->getHeight() . 'px');
        $block->setLeft($powerPointShape->getOffsetX() . 'px');
        $block->setTop($powerPointShape->getOffsetY() . 'px');

        $shapeImageFilePath = $this->getRootImagesPath() . '/' . $powerPointShape->getIndexedFilename();
        file_put_contents($shapeImageFilePath, $powerPointShape->getContents());

        $imageFilePath = $this->convertImage($shapeImageFilePath);

        $block->setFilePath($this->getImagesPath() . '/' . $imageFilePath);
        $block->setImageSize($this->getRootImagesPath() . '/' . $imageFilePath, $powerPointShape->getWidth(), $powerPointShape->getHeight());
        $block->setNaturalImageSizeFromFile($this->getRootImagesPath() . '/' . $imageFilePath);

        $slide->addBlock($block);
    }

    protected function loadShapeText(RichText $powerPointShape, Slide $slide): void
    {
        $block = new TextBlock();
        if ($this->currentSlideBlockNumber === 1 && ($this->currentSlideNumber === 0 || $this->currentSlideNumber === $this->slideNumber - 1)) {
            $block->setType(AbstractBlock::TYPE_HEADER);
            $block->setWidth('1200px');
            $block->setHeight('auto');
            $block->setLeft('14px');
            $block->setTop('294px');
            $block->setFontSize('3em');
        }
        else {
            $block->setType(AbstractBlock::TYPE_TEXT);
            $block->setWidth($powerPointShape->getWidth() . 'px');
            $block->setHeight('auto');
            $block->setLeft($powerPointShape->getOffsetX() . 'px');
            $block->setTop($powerPointShape->getOffsetY() . 'px');
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

    protected function convertImage(string $filePath)
    {
        [$imageWidth, $imageHeight, $type] = getimagesize($filePath);
        if ((int)$type !== IMAGETYPE_PNG) {
            return $filePath;
        }
        $newFilePath = str_replace('.png', '.jpg', $filePath);
        Image::resize($filePath, $imageWidth, $imageHeight)->save($newFilePath, ['jpeg_quality' => 80]);
        unlink($filePath);
        return basename($newFilePath);
    }

}