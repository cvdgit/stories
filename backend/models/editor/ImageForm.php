<?php


namespace backend\models\editor;


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

    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            ['image', 'image', 'maxSize' => 50 * 1024 * 1024],
            [['action', 'actionSlideID', 'actionStoryID', 'back_to_next_slide', 'story_id'], 'integer'],
            [['imagePath', 'what', 'imageID'], 'string'],
            ['url', 'url'],
        ]);
        return $rules;
    }

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();
        $labels = array_merge($labels, [
            'image' => 'Изображение на слайде',
            'action' => 'Выполнить действие',
            'actionStoryID' => 'История',
            'actionSlideID' => 'Слайд',
            'back_to_next_slide' => 'Возврат на текущий слайд',
            'url' => 'Ссылка на изображение',
        ]);
        return $labels;
    }

    public function upload(string $path)
    {
        if (!$this->image->saveAs($path)) {
            throw new \DomainException('Slide image upload error');
        }
    }

}