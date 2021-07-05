<?php

namespace backend\services;

use yii\helpers\FileHelper;
use yii\imagine\Image;

class ImageResizeService
{

    public function resizeSlideImage(string $imagePath, $width, $height): string
    {
        $newImageFileName = $this->createNewImageFileName($imagePath);
        Image::resize($imagePath, $width, $height)->save($newImageFileName, ['quality' => 90]);
        FileHelper::unlink($imagePath);
        return $newImageFileName;
    }

    private function createNewImageFileName(string $imagePath): string
    {
        $parts = pathinfo($imagePath);
        return $parts['dirname'] . DIRECTORY_SEPARATOR . md5(random_int(0, 9999) . time() . random_int(0, 9999)) . '.' . $parts['extension'];
    }
}