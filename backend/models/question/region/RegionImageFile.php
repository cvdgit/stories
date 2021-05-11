<?php

namespace backend\models\question\region;

use Yii;
use yii\helpers\FileHelper;

class RegionImageFile
{

    private $extension;
    private $name;
    private $prefix = '_mini';

    public function __construct(string $extension)
    {
        $this->extension = $extension;
        $this->name = Yii::$app->security->generateRandomString();
    }

    public function createImageFileName(string $folder, bool $withPrefix = true): string
    {
        FileHelper::createDirectory($folder);

        return $folder . $this->getFileName($withPrefix);
    }

    public function getFileName(bool $withPrefix = true): string
    {
        return $this->name . ($withPrefix ? $this->prefix : '') . '.' . $this->extension;
    }

}