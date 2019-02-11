<?php

namespace common\services;

use yii;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Shape\RichText;

class StoryPowerPointService
{

	protected function getSlideHtml($args)
	{
		return vsprintf('<section data-id="373b64b98ac710935dfbdcbab5e10ae3" data-background-color="#000000">
          <div class="sl-block" data-block-type="image" style="min-width: 4px; min-height: 4px; width: 973px; height: 720px; left: 0px; top: 0px;" data-block-id="65b8a7e6d1bddee449a0ecfd80a09c55">
            <div class="sl-block-content" style="z-index: 11;">
              <img style="" data-natural-width="1411" data-natural-height="1044" data-src="%1$s">
            </div>
          </div>
          <div class="sl-block" data-block-type="text" style="height: auto; min-width: 30px; min-height: 30px; width: 290px; left: 983px; top: 9px;" data-block-id="25fdd2cdf70f9ce9756d1d164a9cd02f">
            <div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 12;">
              <p><span style="color:#FFFFFF">%2$s</span></p>
            </div>
          </div>
        </section>', $args);
	}

	protected function getSlideHtml2($args)
	{
		return vsprintf('<section data-id="fc8f721ab30e503a6ec254abb08f859e" data-background-color="#000000">
          <div class="sl-block" data-block-type="image" style="min-width: 4px; min-height: 4px; width: 1459px; height: 1080px; left: 0px; top: 0px;" data-block-id="ab9fb6dc12f73574405bfac74947da8f">
            <div class="sl-block-content" style="z-index: 11;">
              <img style="" data-natural-width="1459" data-natural-height="1080" data-src="%1$s">
            </div>
          </div>
          <div class="sl-block" data-block-type="text" style="height: auto; min-width: 30px; min-height: 30px; width: 446px; left: 1467px; top: 8px;" data-block-id="81787eb42185bcd23746e5195a2d6f6f">
            <div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 12;">
              <p style="text-align:left"><span style="color:#FFFFFF;font-size:1.4em">%2$s</span></p>
            </div>
          </div>
        </section>', $args);
	}

  protected function getSlideHtmlFirstPage($args) {
    return vsprintf('<section data-id="0255dfb727c7c71606fe1b6d67b0c098" data-background-color="#000000">
          <div class="sl-block" data-block-type="text" style="width: 1774px; left: 73px; top: 814px; height: auto;" data-block-id="6e809638da9a6ad00b8c150ef8fbe827">
            <div class="sl-block-content" data-placeholder-tag="h1" data-placeholder-text="Title Text" style="color: rgb(255, 255, 255); z-index: 11;" dir="ui">
              <h1><strong><span style="font-size:2.5em">%1$s</span></strong></h1>
            </div>
          </div>
          <div class="sl-block" data-block-type="image" style="min-width: 4px; min-height: 4px; width: 1146px; height: 848px; left: 388px; top: 0px;" data-block-id="5d002916098ba2191de37cea98890e2b">
            <div class="sl-block-content" style="z-index: 12;">
              <img style="" data-natural-width="1825" data-natural-height="1350" data-lazy-loaded="" data-src="%2$s">
            </div>
          </div>
        </section>', $args);
  }

	public function createStoryFromPowerPoint($fileName)
	{

        $localFolder = Yii::getAlias('@public') . '/slides/' . $fileName . '/';
        if (!file_exists($localFolder)) {
            mkdir($localFolder, 0777);
        }
        else {
            array_map('unlink', glob($localFolder . "*.*"));
        }

        $reader = IOFactory::createReader('PowerPoint2007');
        $presentation = $reader->load(Yii::getAlias('@public') . '/slides_file/' . $fileName);
        
        $slides = $presentation->getAllSlides();
        $slideIndex = 1;
        $html = '';
        foreach ($slides as $slide) {

          $isFirstSlide = ($slideIndex == 1);

        	$shapes = $slide->getShapeCollection();
        	$slideImageFilePath = '';
        	$slideText = '';
        	foreach ($shapes as $shape) {

        		if (get_class($shape) == 'PhpOffice\PhpPresentation\Shape\Drawing\Gd') {
        			file_put_contents($localFolder . $shape->getIndexedFilename(), $shape->getContents());
        			$slideImageFilePath = '/slides/' . $fileName . '/' . $shape->getIndexedFilename();
        		}

        		if (get_class($shape) == 'PhpOffice\PhpPresentation\Shape\RichText') {
        			$slideText = $shape->getPlainText();
        		}
        	}

          //if ($isFirstSlide) {
          //  $slideHtml = $this->getSlideHtmlFirstPage([$slideText, $slideImageFilePath]);
          //}
          //else {
        	  $slideHtml = $this->getSlideHtml2([$slideImageFilePath, $slideText]);
          //}
        	$html .= $slideHtml;

          $slideIndex++;
        }

        return '<div class="slides">' . $html . '</div>';
	}

}
