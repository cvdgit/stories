<?php

namespace backend\components\image;

use Yii;
use yii\helpers\FileHelper;

class EditorImage extends BaseImage
{

    public function __construct()
    {
        $this->folder = Yii::$app->formatter->asDate('now', 'MM-yyyy');
        $this->rootFolder = Yii::$app->params['images.root'][self::ROOT_EDITOR];
        $this->rootFolderID = self::ROOT_EDITOR;
        parent::__construct();
    }

    public function getImagePath(): string
    {
        return FileHelper::normalizePath($this->public)
            . FileHelper::normalizePath($this->rootFolder)
            . DIRECTORY_SEPARATOR
            . FileHelper::normalizePath($this->folder);
    }
}
