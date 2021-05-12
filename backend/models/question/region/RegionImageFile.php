<?php

namespace backend\models\question\region;

use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class RegionImageFile
{

    public const ORIGINAL = '_orig';

    private $uploadedFile;
    private $regionImage;
    private $name;

    public function __construct(UploadedFile $uploadedFile, RegionImage $regionImage)
    {
        $this->uploadedFile = $uploadedFile;
        $this->regionImage = $regionImage;
        $this->name = Yii::$app->security->generateRandomString();
    }

    public function save(string $prefix = ''): string
    {
        $imagePath = $this->createImageFileName($prefix);
        $this->uploadedFile->saveAs($imagePath);
        return $imagePath;
    }

    public function saveOriginal(): string
    {
        return $this->save(self::ORIGINAL);
    }

    public function createImageFileName(string $prefix = ''): string
    {
        $folder = $this->regionImage->getImagesPath();
        FileHelper::createDirectory($folder);
        return $folder . $this->getFileName($prefix);
    }

    public function getFileName(string $prefix = ''): string
    {
        return $this->name . $prefix . '.' . $this->uploadedFile->extension;
    }

}