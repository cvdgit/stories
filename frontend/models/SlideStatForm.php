<?php

namespace frontend\models;

use common\models\StoryStatistics;
use yii\base\Model;

class SlideStatForm extends Model
{

    public $story_id;
    public $slide_number;
    public $begin_time;
    public $end_time;
    public $chars;
    public $session;
    public $slide_id;
    public $last_slide;
    public $study_task_id;

    public function rules()
    {
        return [
            [['story_id', 'slide_id', 'begin_time', 'end_time', 'chars', 'session'], 'required'],
            [['story_id', 'slide_number', 'begin_time', 'end_time', 'chars', 'slide_id', 'last_slide', 'study_task_id'], 'integer'],
            [['session'], 'string', 'max' => 50],
        ];
    }

    public function saveStat(int $userID = null): void
    {
        if (!$this->validate()) {
            throw new \DomainException('SlideStatForm not valid');
        }
        $model = StoryStatistics::create(
            $this->story_id,
            $this->slide_id,
            $this->session,
            $this->slide_number,
            $this->begin_time,
            $this->end_time,
            $this->chars,
            $userID
        );
        $model->save();
    }

    public function needUpdateStudyTaskStatus(): bool
    {
        return !empty($this->study_task_id);
    }

    public function isLastSlide(): bool
    {
        return (int) $this->last_slide === 1;
    }
}
