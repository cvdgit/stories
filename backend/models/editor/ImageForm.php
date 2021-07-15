<?php

namespace backend\models\editor;

use backend\models\ImageSlideBlock;
use common\models\StorySlide;
use common\models\StorySlideImage;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class ImageForm extends BaseForm
{

    /* @var UploadedFile */
    public $image;

    public $imagePath;
    public $fullImagePath;

    public $action;
    public $actionStoryID;
    public $actionSlideID;
    public $back_to_next_slide;

    public $story_id;
    public $imageID;
    public $what;

    public $url;

    /** @var StorySlideImage */
    public $imageModel;

    public $image_id;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['image', 'image', 'maxSize' => 50 * 1024 * 1024],
            [['action', 'actionSlideID', 'actionStoryID', 'back_to_next_slide', 'story_id', 'image_id'], 'integer'],
            [['imagePath', 'what', 'imageID'], 'string'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'image' => 'Изображение на слайде',
            'action' => 'Выполнить действие',
            'actionStoryID' => 'История',
            'actionSlideID' => 'Слайд',
            'back_to_next_slide' => 'Возврат на текущий слайд',
        ]);
    }

    public function upload(string $path): void
    {
        FileHelper::createDirectory(pathinfo($path, PATHINFO_DIRNAME));
        if (!$this->image->saveAs($path)) {
            throw new \DomainException('Slide image upload error');
        }
    }

    public function afterCreate(StorySlide $slideModel): void
    {
        if (!empty($this->imagePath)) {
            $model = ImageSlideBlock::create($this->imageModel->id, $this->slide_id, $this->block_id);
            $model->save();
        }
    }
}
