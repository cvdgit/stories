<?php


namespace backend\models\editor;


use yii\base\Model;

class BaseForm extends Model
{

    public $story_id;
    public $slide_index;
    public $block_id;

    public $left;
    public $top;
    public $width;
    public $height;

    public $view;

    public function rules(): array
    {
        return [
            [['story_id', 'slide_index'], 'required'],
            [['story_id', 'slide_index'], 'integer'],
            [['block_id', 'left', 'top', 'width', 'height'], 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'left' => 'Левый отступ',
            'top' => 'Правый отступ',
            'width' => 'Ширина',
            'height' => 'Высота',
        ];
    }

}