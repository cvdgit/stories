<?php

namespace backend\models\test;

use common\models\StoryTestQuestion;
use common\models\StoryTestQuestionStorySlide;
use common\models\UserRecentStory;
use Yii;
use yii\base\Model;

class QuestionSlidesForm extends Model
{

    public $question_id;
    public $slide_ids = [];

    public function rules()
    {
        return [
            ['question_id', 'integer'],
            ['slide_ids', 'each', 'rule' => ['integer']],
        ];
    }

    public function create(StoryTestQuestion $questionModel): void
    {
        if (!$this->validate()) {
            throw new \DomainException('QuestionSlidesForm is not valid');
        }
        StoryTestQuestionStorySlide::deleteByQuestionID($questionModel->id);
        $order = 1;
        foreach ($this->slide_ids as $slideID) {

            $model = StoryTestQuestionStorySlide::create($questionModel->id, $slideID, $order);
            $model->save();

            $recentModel = UserRecentStory::createRecent(Yii::$app->user->id, $model->storySlide->story_id);
            if (!$recentModel->save()) {
                throw new \DomainException('Recent model save error');
            }

            $order++;
        }
        $questionModel->refresh();
    }
}
