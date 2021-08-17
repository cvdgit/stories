<?php

namespace common\components;

use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\imagine\Image;

class StoryCover
{

	public static function createListThumbnail($filePath)
	{
		$thumbnailFilePath = Yii::getAlias('@public') . self::getListThumbPath(basename($filePath));
		Image::thumbnail($filePath, 330, 245, ManipulatorInterface::THUMBNAIL_INSET)
            ->save($thumbnailFilePath, ['quality' => 100]);
		return $thumbnailFilePath;
	}

	public static function createStoryThumbnail($filePath)
	{
		$thumbnailFilePath = Yii::getAlias('@public') . self::getStoryThumbPath(basename($filePath));
		Image::thumbnail($filePath, 973, 720, ManipulatorInterface::THUMBNAIL_INSET)
            ->save($thumbnailFilePath, ['quality' => 100]);
		return $thumbnailFilePath;
	}

	public static function create($filePath)
	{
		self::createListThumbnail($filePath);
		self::createStoryThumbnail($filePath);
	}

	public static function delete($cover)
	{
		$coverPath = self::getSourceFilePath($cover, true);
		if (file_exists($coverPath)) {
			unlink($coverPath);
		}
		$listThumbnailPath = self::getListThumbPath($cover, true);
		if (file_exists($listThumbnailPath)) {
			unlink($listThumbnailPath);
		}
		$storyThumbnailPath = self::getStoryThumbPath($cover, true);
		if (file_exists($storyThumbnailPath)) {
			unlink($storyThumbnailPath);
		}
	}

	public static function getCoverFolderPath($absolute = false): string
	{
		return ($absolute ? Yii::getAlias('@public') : '') . '/' . Yii::$app->params['coverFolder'];
	}

	public static function getSourceFilePath($cover, $absolute = false): string
	{
		return self::getCoverFolderPath($absolute) . '/' . $cover;
	}

	public static function getListThumbPath($cover, $absolute = false): string
	{
	    if (empty($cover)) {
	        return '/img/story-1.jpg';
        }
		return self::getCoverFolderPath($absolute) . '/list/' . $cover;
	}

	public static function getStoryThumbPath($cover, $absolute = false): string
	{
		return self::getCoverFolderPath($absolute) . '/story/' . $cover;
	}
}
