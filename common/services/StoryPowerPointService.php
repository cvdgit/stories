<?php

namespace common\services;

use yii;

use backend\models\SourcePowerPointForm;

use backend\components\Story;
use backend\components\markup\BlockImageMarkup;

use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Shape\RichText;

class StoryPowerPointService
{

    protected $story;
    protected $filename;
    protected $imagesFolder;

    private function clearImagesFolder()
    {
        $folder = $this->getImagesFolder();
        if (!file_exists($folder)) {
            mkdir($folder, 0777);
        }
        else {
            array_map('unlink', glob($folder . "*.*"));
        }
    }

    private function getImagesFolder($relativePath = false)
    {
        return ($relativePath ? '' : Yii::getAlias('@public')) . '/slides/' . $this->imagesFolder . '/';
    }

    public function loadStory(SourcePowerPointForm $model)
    {
        $this->story = new Story();
        $this->filename = Yii::getAlias('@public') . '/slides_file/' . $model->storyFile;
        $this->imagesFolder = $model->storyFile;
        $this->clearImagesFolder();

        $reader = IOFactory::createReader('PowerPoint2007');
        $pptxPresentation = $reader->load($this->filename);
        $this->loadSlides($pptxPresentation);

        return $this->story;
    }

    protected function loadSlides($pptxPresentation)
    {
        $pptxSlides = $pptxPresentation->getAllSlides();
        $slideIndex = 1;
        $slideCount = count($pptxSlides);
        foreach ($pptxSlides as $pptxSlide) {
            $onlyTextLayout = ($slideIndex == 1 || $slideIndex == $slideCount);
            $this->loadSlide($pptxSlide, $onlyTextLayout);
            $slideIndex++;
        }
    }

    protected function loadSlide($pptxSlide, $onlyTextLayout)
    {
        $slide = $this->story->createSlide();
        $slide->setLayout($onlyTextLayout);
        $slide->setSlideMarkup();

        $pptxShapes = $pptxSlide->getShapeCollection();
        $this->loadSlideShapes($slide, $pptxShapes);
    }

    protected function loadShapeImage($pptxShape, $slide)
    {
        $shapeImageFilePath = $this->getImagesFolder() . $pptxShape->getIndexedFilename();
        file_put_contents($shapeImageFilePath, $pptxShape->getContents());

        $block = $slide->createBlockImage();
        $markup = $block->createBlockImageMarkup(true);
        
        $src = $this->getImagesFolder(true) . $pptxShape->getIndexedFilename();
        $markup->setImage($src);
        
        $width = $pptxShape->getWidth();
        if ($width > BlockImageMarkup::DEFAULT_IMAGE_WIDTH) {
            $width = BlockImageMarkup::DEFAULT_IMAGE_WIDTH;
        }

        $height = $pptxShape->getHeight();
        if ($height > BlockImageMarkup::DEFAULT_IMAGE_HEIGHT) {
            $height = BlockImageMarkup::DEFAULT_IMAGE_HEIGHT;
        }

        $markup->setWidth($width . 'px');
        $markup->setHeight($height . 'px');

        $block->setSrc($src);
    }

    protected function loadShapeText($pptxShape, $slide)
    {
        $block = $slide->createBlockText();
        if ($slide->getLayout()) {
            $markup = $block->createBlockHeaderMarkup(true);
        }
        else {
            $markup = $block->createBlockTextMarkup(true);
        }

        $paragraphText = [];
        foreach ($pptxShape->getParagraphs() as $paragraph) {
            $text = $paragraph->getPlainText();
            if (strlen($text) > 0) {
                $paragraphText[] = $text;
            }
        }

        $markup->setText(implode('<br/>', $paragraphText));
        
        /*
        $markup->setWidth($pptxShape->getWidth() . 'px');
        $markup->setHeight($pptxShape->getHeight() . 'px');
        $markup->setLeft($pptxShape->getOffsetX() . 'px');
        $markup->setTop($pptxShape->getOffsetY() . 'px');
        */

        $block->setText(implode("\r\n", $paragraphText));
    }

    protected function loadSlideShapes($slide, $pptxShapes)
    {
        foreach ($pptxShapes as $pptxShape) {
            $className = get_class($pptxShape);
            switch ($className) {
                case 'PhpOffice\PhpPresentation\Shape\Drawing\Gd':
                    $this->loadShapeImage($pptxShape, $slide);
                    break;
                case 'PhpOffice\PhpPresentation\Shape\RichText':
                    $this->loadShapeText($pptxShape, $slide);
                default:
            }
        }
    }

}
