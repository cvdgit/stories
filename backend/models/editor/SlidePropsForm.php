<?php


namespace backend\models\editor;


use yii\base\Model;

class SlidePropsForm extends Model
{

    public $story_id;

    public $slide_index;
    public $slide_id;

    public $hidden;

    public function rules(): array
    {
        return [
            [['story_id', 'slide_index'], 'required'],
            [['story_id', 'slide_index', 'hidden'], 'integer'],
            [['slide_id'], 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [

        ];
    }
}