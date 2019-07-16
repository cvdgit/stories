<?php


namespace frontend\widgets;


use common\models\Story;
use yii\base\Widget;

class FollowingStories extends Widget
{

    public $storyID;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $model = Story::findModel($this->storyID);
        $categoryIDs = array_map(function($category) {
            return $category->id;
        }, $model->categories);
        return $this->render('_following_stories', [
            'models' => Story::followingStories($categoryIDs),
        ]);
    }

}