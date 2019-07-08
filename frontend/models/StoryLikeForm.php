<?php


namespace frontend\models;


use yii\base\Model;

class StoryLikeForm extends Model
{

    public $like;
    public $story_id;

    public function rules()
    {
        return [
            ['like', 'integer'],
            ['story_id', 'integer'],
        ];
    }

}