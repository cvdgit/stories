<?php


namespace frontend\widgets;


use common\services\StoryLikeService;
use frontend\models\StoryLikeForm;
use yii\base\Widget;
use Yii;

class StoryLikeWidget extends Widget
{

    public $storyId;

    protected $likeService;

    public function __construct(StoryLikeService $likeService, $config = [])
    {
        $this->likeService = $likeService;
        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
    }

    public function run()
    {

        $likeNumber = $this->likeService->getLikeCount($this->storyId);
        $dislikeNumber = $this->likeService->getDislikeCount($this->storyId);

        $form = new StoryLikeForm();
        $form->story_id = $this->storyId;

        $like = false;
        $dislike = false;
        if (!Yii::$app->user->isGuest) {
            $action = $this->likeService->getUserStoryAction($this->storyId, Yii::$app->user->id);
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