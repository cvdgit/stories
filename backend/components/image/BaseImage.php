<?php

namespace backend\components\image;

use common\models\StorySlideImage;
use Yii;
use yii\helpers\FileHelper;

class BaseImage
{

    public const ROOT_EDITOR = 1;
    public const ROOT_POWERPOINT = 2;

    protected $rootFolder;
    protected $rootFolderID;
    protected $folder;
    protected $public;

    public function __construct()
    {
        $this->public = Yii::getAlias('@public');
    }

    public function createSlideBlockLink(int $imageID, int $slideID, string $blockID): void
    {
        $command = Yii::$app->db->createCommand();
        $command->insert('{{%image_slide_block}}', [
            'image_id' => $imageID,
            'slide_id' => $slideID,
            'block_id' => $blockID,
        ]);
        $command->execute();
    }

    public function create(string $filePath): StorySlideImage
    {
        $model = StorySlideImage::createImage($this->rootFolderID, $this->folder, basename($filePath), FileHelper::getMimeType($filePath));
        $model->save();
        return $model;
    }
}
