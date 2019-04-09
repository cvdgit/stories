<?php

namespace common\components;

use Yii;
use yii\imagine\Image;

class StoryCover
{

	public static function createListThumbnail($filePath)
	{
		$thumbnailFilePath = Yii::getAlias('@public') . self::getListThumbPath(basename($filePath));
		Image::thumbnail(self::getSourceFilePath($filePath), 330, 245, 'inset')->save($thumbnailFilePath, ['jpeg_quality' => 100]);
		return $thumbnailFilePath;
	}

	public static function createStoryThumbnail($filePath)
	{
		$thumbnailFilePath = Yii::getAlias('@public') . self::getStoryThumbPath(basename($filePath));
		Image::thumbnail(self::getSourceFilePath($filePath), 973, 720, 'inset')->save($thumbnailFilePath, ['jpeg_quality' => 100]);
		return $thumbnailFilePath;
	}

	public static function create($filePath)
	{
		self::createListThumbnail($filePath);
		self::createStoryThumbnail($filePath);
	}

	public static function getSourceFilePath($cover)
	{
		return Yii::getAlias('@public') . '/' . Yii::$app->params['coverFolder'] . '/' . $cover;
	}

	public static function getListThumbPath($cover)
	{
		return '/' . Yii::$app->params['coverFolder'] . '/list/' . $cover;
	}

	public static function getStoryThumbPath($cover)
	{
		return '/' . Yii::$app->params['coverFolder'] . '/story/' . $cover;
	}

}
