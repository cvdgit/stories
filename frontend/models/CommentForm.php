<?php


namespace frontend\models;


use common\models\Comment;
use yii\base\Model;
use common\models\Story;

class CommentForm extends Model
{

    public $body;
    public $story_id;

    public function rules()
    {
        return [
            [['body'], 'required'],
            [['story_id'], 'integer'],
            [['story_id'], 'exist', 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
        ];
    }

    public function createComment($userId)
    {
        $comment = new Comment();
        $comment->story_id = $this->story_id;
        $comment->user_id = $userId;
        $comment->body = $this->body;
        return $comment->save();
    }

}