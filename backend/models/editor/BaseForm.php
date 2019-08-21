<?php


namespace backend\models\editor;


use yii\base\Model;

class BaseForm extends Model
{

    public $slide_id;
    public $block_id;

    public $left;
    public $top;
    public $width;
    public $height;

    public $view;

    public function rules(): array
    {
        return [
            [['slide_id'], 'required'],
            [['slide_id'], 'integer'],
            [['block_id', 'left', 'top', 'width', 'height'], 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'left' => 'Левый отступ',
            'top' => 'Верхний отступ',
            'width' => 'Ширина',
            'height' => 'Высота',
        ];
    }

}