<?php

namespace common\services;

use yii;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Shape\RichText;

class StoryPowerPointService
{

  public $defaultImageWidth = 973; // 1459
  public $defaultImageHeight = 720; // 1080

  protected function getSlideImageSize()
  {
    return 'data-natural-width="1459" data-natural-height="1080"';
  }

  protected function getWrapperSlideImageSize($width, $height)
  {
    if ($width > $this->defaultImageWidth) {
      $width = $this->defaultImageWidth;
    }
    if ($height > $this->defaultImageHeight) {
      $height = $this->defaultImageHeight;
    }
    return "width: ${width}px; height: ${height}px;";
  }

/*
	protected function getSlideHtml($args)
	{
		return vsprintf('<section data-id="fc8f721ab30e503a6ec254abb08f859e" data-background-color="#000000">
          <div class="sl-block" data-block-type="image" style="min-width: 4px; min-height: 4px; %4$s left: 0px; top: 0px;" data-block-id="ab9fb6dc12f73574405bfac74947da8f">
            <div class="sl-block-content" style="z-index: 11;">
              <img %3$s data-src="%1$s">
            </div>
          </div>
          <div class="sl-block" data-block-type="text" style="height: auto; min-width: 30px; min-height: 30px; width: 446px; left: 1467px; top: 8px;" data-block-id="81787eb42185bcd23746e5195a2d6f6f">
            <div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 12;">
              <p style="text-align:left"><span style="color:#FFFFFF;font-size:1.4em">%2$s</span></p>
            </div>
          </div>
        </section>', $args);
	}
*/

  protected function getSlideHtml($args)
  {
    return vsprintf('<section data-id="373b64b98ac710935dfbdcbab5e10ae3" data-background-color="#000000">
                       <div class="sl-block" data-block-type="image" style="min-width: 4px; min-height: 4px; %3$s left: 0px; top: 0px;" data-block-id="65b8a7e6d1bddee449a0ecfd80a09c55">
                         <div class="sl-block-content" style="z-index: 11;">
                           <img data-natural-width="1459" data-natural-height="1080" data-src="%1$s">
                         </div>
                       </div>
                       <div class="sl-block" data-block-type="text" style="height: auto; min-width: 30px; min-height: 30px; width: 290px; left: 983px; top: 9px;" data-block-id="25fdd2cdf70f9ce9756d1d164a9cd02f">
                         <div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 12;">
                           <p><span style="color:#FFFFFF;font-size:0.8em">%2$s</span></p>
                         </div>
                       </div>
                     </section>', $args);
  }

  protected function getSlideHtmlFirstPage($args) {
    return vsprintf('<section data-id="78734c628233665b008026a39e547565" data-background-color="#000000">
                       <div class="sl-block" data-block-type="text" style="width: 1200px; left: 14px; top: 294px; height: auto;" data-block-id="5beee66d40239b40ceb882ff639d5dd4">
                         <div class="sl-block-content" data-placeholder-tag="h1" data-placeholder-text="Title Text" style="color: rgb(255, 255, 255); z-index: 10;text-align: center">
                           <h1><strong><span style="font-size:1.5em">%1$s</span></strong></h1>
                         </div>
                       </div>
                     </section>', $args);
  }

	public function createStoryFromPowerPoint(\backend\models\SourcePowerPointForm $model)
	{

    $localFolder = Yii::getAlias('@public') . '/slides/' . $model->storyFile . '/';
    if (!file_exists($localFolder)) {
      mkdir($localFolder, 0777);
    }
    else {
      array_map('unlink', glob($localFolder . "*.*"));
    }

    $reader = IOFactory::createReader('PowerPoint2007');
    $presentation = $reader->load(Yii::getAlias('@public') . '/slides_file/' . $model->storyFile);

    $slides = $presentation->getAllSlides();
    $slideIndex = 1;
    $slideCount = sizeof($slides);
    $html = '';
    foreach ($slides as $slide) {

      $isFirstSlide = ($slideIndex == 1);
      $isLastSlide = ($slideIndex == $slideCount);

    	$shapes = $slide->getShapeCollection();
    	$slideImageFilePath = '';
    	$slideText = '';
      $imageWidth = 0;
      $imageHeight = 0;
    	foreach ($shapes as $shape) {

    		if (get_class($shape) == 'PhpOffice\PhpPresentation\Shape\Drawing\Gd') {
    			file_put_contents($localFolder . $shape->getIndexedFilename(), $shape->getContents());
    			$slideImageFilePath = '/slides/' . $model->storyFile . '/' . $shape->getIndexedFilename();
          $imageWidth = $shape->getWidth();
          $imageHeight = $shape->getHeight();
    		}

    		if (get_class($shape) == 'PhpOffice\PhpPresentation\Shape\RichText') {
    			$slideText = $shape->getPlainText();
    		}
    	}

      if ($model->firstSlideTemplate && $isFirstSlide) {
        $slideHtml = $this->getSlideHtmlFirstPage([$slideText, $slideImageFilePath]);
      }
      else if ($model->lastSlideTemplate && $isLastSlide) {
        $slideHtml = $this->getSlideHtmlFirstPage([$slideText, $slideImageFilePath]);
      }
      else {
        $slideImageSize = $this->getSlideImageSize();
        $wrapperSlideImageSize = $this->getWrapperSlideImageSize($imageWidth, $imageHeight);
    	  $slideHtml = $this->getSlideHtml([$slideImageFilePath, $slideText, $wrapperSlideImageSize]);
      }
    	$html .= $slideHtml;

      $slideIndex++;
    }
    return '<div class="slides">' . $html . '</div>';
	}

}
