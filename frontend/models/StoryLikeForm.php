<?php


namespace frontend\models;


use yii\base\Model;

class StoryLikeForm extends Model
{

    const LIKE = 1;
    const DISLIKE = 0;

    public $like;
    public $story_id;

    public function rules()
    {
        return [
            ['like', 'in', 'range' => [self::LIKE, self::DISLIKE]],
            ['story_id', 'integer'],
        ];
    }

}