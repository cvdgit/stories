<?php


namespace backend\models\editor;


use yii\base\Model;

class BaseForm extends Model
{

    public $story_id;
    public $slide_index;

    public function rules(): array
    {
        return [
            [['story_id', 'slide_index'], 'required'],
            [['story_id', 'slide_index'], 'integer'],
        ];
    }

}