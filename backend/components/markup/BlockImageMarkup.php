<?php

namespace backend\components\markup;

class BlockImageMarkup extends BlockMarkup
{

	protected $defaultMarkup = [
		'tagName' => 'div',
		'attributes' => [
			'data-block-id' => '',
			'class' => 'sl-block',
			'data-block-type' => 'image',
			'style' => 'min-width: 4px; min-height: 4px; width: 973px; height: 720px; left: 0px; top: 0px;',
		],
	];

	public function setImageSize($imagePath, $imageWidth = 0, $imageHeight = 0)
	{
		if ($imageWidth == 0 && $imageHeight == 0) {
			list($imageWidth, $imageHeight) = getimagesize(\Yii::getAlias('@public') . $imagePath);
		}

		$ratio = $imageWidth / $imageHeight;
		if (ImageMarkup::DEFAULT_IMAGE_WIDTH / ImageMarkup::DEFAULT_IMAGE_HEIGHT > $ratio) {
			$imageWidth = ImageMarkup::DEFAULT_IMAGE_HEIGHT * $ratio;
			$imageHeight = ImageMarkup::DEFAULT_IMAGE_HEIGHT;
		}
		else {
			$imageHeight = ImageMarkup::DEFAULT_IMAGE_WIDTH / $ratio;
			$imageWidth = ImageMarkup::DEFAULT_IMAGE_WIDTH;
		}

		$this->setWidth("{$imageWidth}px");
		$this->setHeight("{$imageHeight}px");
	}

}
