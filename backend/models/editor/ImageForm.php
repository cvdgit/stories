<?php


namespace backend\models\editor;


class ImageForm extends BaseForm
{

    public $image;

    public $imagePath;
    public $fullImagePath;

    public $action;
    public $actionStoryID;
    public $actionSlideID;
    public $back_to_next_slide;

    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            ['image', 'image', 'maxSize' => 50 * 1024 * 1024],
            [['action', 'actionSlideID', 'actionStoryID', 'back_to_next_slide'], 'integer'],
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
        ]);
        return $labels;
    }

}