<?php

namespace common\services;

use yii;

use backend\models\SourcePowerPointForm;
use backend\components\Story;

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

        $slideMarkup = new \backend\components\markup\SlideMarkup($slide);
        $slide->setMarkup($slideMarkup);

        $pptxShapes = $pptxSlide->getShapeCollection();
        $this->loadSlideShapes($slide, $pptxShapes);
    }

    protected function loadShapeImage($pptxShape, $slide)
    {
        $shapeImageFilePath = $this->getImagesFolder() . $pptxShape->getIndexedFilename();
        file_put_contents($shapeImageFilePath, $pptxShape->getContents());

        $block = $slide->createBlockImage();

        $blockMarkup = new \backend\components\markup\BlockImageMarkup($block);
        $blockContentMarkup = new \backend\components\markup\BlockImageContentMarkup($block);
        $imageMarkup = new \backend\components\markup\ImageMarkup($block);

        $imagePath = $this->getImagesFolder(true) . $pptxShape->getIndexedFilename();
        $imageMarkup->setImagePath($imagePath);

        $width = $pptxShape->getWidth();
        if ($width > \backend\components\markup\ImageMarkup::DEFAULT_IMAGE_WIDTH) {
            $width = \backend\components\markup\ImageMarkup::DEFAULT_IMAGE_WIDTH;
        }
        $imageMarkup->setWidth($width . 'px');

        $height = $pptxShape->getHeight();
        if ($height > \backend\components\markup\ImageMarkup::DEFAULT_IMAGE_HEIGHT) {
            $height = \backend\components\markup\ImageMarkup::DEFAULT_IMAGE_HEIGHT;
        }
        $imageMarkup->setHeight($height . 'px');

        $blockContentMarkup->addElement($imageMarkup);
        $blockMarkup->addElement($blockContentMarkup);

        $block->setMarkup($blockMarkup);
    }

    protected function loadShapeText($pptxShape, $slide)
    {
        
        $block = $slide->createBlockText();

        if ($slide->getLayout()) {
            $blockMarkup = new \backend\components\markup\BlockHeaderMarkup($block);
            $blockContentMarkup = new \backend\components\markup\BlockHeaderContentMarkup($block);
            $paragraphMarkup = new \backend\components\markup\HeaderMarkup($block);
        }
        else {
            $blockMarkup = new \backend\components\markup\BlockMarkup($block);
            $blockContentMarkup = new \backend\components\markup\BlockContentMarkup($block);
            $paragraphMarkup = new \backend\components\markup\ParagraphMarkup($block);
        }

        $paragraphText = [];
        foreach ($pptxShape->getParagraphs() as $paragraph) {
            $text = $paragraph->getPlainText();
            if (strlen($text) > 0) {
                $paragraphText[] = $text;
            }
        }
        $paragraphMarkup->setContent(implode('<br/>', $paragraphText));

        $blockContentMarkup->addElement($paragraphMarkup);
        $blockMarkup->addElement($blockContentMarkup);

        $block->setMarkup($blockMarkup);
        
        /*
        $markup->setWidth($pptxShape->getWidth() . 'px');
        $markup->setHeight($pptxShape->getHeight() . 'px');
        $markup->setLeft($pptxShape->getOffsetX() . 'px');
        $markup->setTop($pptxShape->getOffsetY() . 'px');
        */
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
