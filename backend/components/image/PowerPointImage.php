<?php

namespace backend\components\image;

use Yii;

class PowerPointImage extends BaseImage
{

    public function __construct(string $folder)
    {
        $this->folder = $folder;
        $this->rootFolder = Yii::$app->params['images.root'][self::ROOT_POWERPOINT];
        $this->rootFolderID = self::ROOT_POWERPOINT;
        parent::__construct();
    }
}
