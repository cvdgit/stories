<?php


namespace frontend\widgets;


use frontend\models\StoryLikeForm;
use yii\base\Widget;
use yii\db\Query;
use Yii;

class StoryLikeWidget extends Widget
{

    public $storyId;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $likeNumber = (new Query())->from('{{%story_like}}')->where('story_id = :id AND action = 1', [':id' => $this->storyId])->count();
        $dislikeNumber = (new Query())->from('{{%story_like}}')->where('story_id = :id AND action = 0', [':id' => $this->storyId])->count();

        $form = new StoryLikeForm();
        $form->story_id = $this->storyId;

        $like = false;
        $dislike = false;
        if (!Yii::$app->user->isGuest) {
            $action = (new Query())
                ->select('action')
                ->from('{{%story_like}}')
                ->where('story_id = :story AND user_id = :user', [
                    ':story' => $this->storyId,
                    ':user' => Yii::$app->user->id,
                ])
                ->scalar();
            $like = ($action === '1');
            $dislike = ($action === '0');
        }

        return $this->render('like', [
            'likeNumber' => $likeNumber,
            'dislikeNumber' => $dislikeNumber,
            'readOnly' => Yii::$app->user->isGuest,
            'model' => $form,
            'like' => $like,
            'dislike' => $dislike,
        ]);
    }

}