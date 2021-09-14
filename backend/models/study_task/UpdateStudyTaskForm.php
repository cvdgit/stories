<?php

namespace backend\models\study_task;

use backend\components\SlideModifier;
use backend\helpers\SelectSlideWidgetHelper;
use common\models\StorySlide;
use common\models\StudyTask;

class UpdateStudyTaskForm extends BaseStudyTaskForm
{

    public $id;
    public $story_id;

    /** @var StudyTask */
    private $model;

    public function rules()
    {
        return array_merge([
            ['story_id', 'required'],
            ['story_id', 'integer'],
        ], parent::rules());
    }

    public function __construct(StudyTask $model, $config = [])
    {
        $this->model = $model;
        $this->loadAttributes();
        parent::__construct($config);
    }

    private function loadAttributes(): void
    {
        $this->id = $this->model->id;
        $this->title = $this->model->title;
        $this->description = $this->model->description;
        $this->status = $this->model->status;
        $this->story_id = $this->model->story_id;
    }

    public function updateTask(): void
    {
        if (!$this->validate()) {
            throw new \DomainException('UpdateStudyTaskForm not valid');
        }
        $this->model->title = $this->title;
        $this->model->description = $this->description;
        $this->model->status = $this->status;
        $this->model->save();

        if (count($this->slide_ids) > 0) {
            $storyID = $this->model->story->id;
            $slideIDs = array_map(static function(int $slideID) {
                $slideModel = StorySlide::findSlide($slideID);
                $id = $slideModel->id;
                if ($slideModel->isLink()) {
                    $id = $slideModel->link_slide_id;
                }
                return $id;
            }, $this->slide_ids);
            $this->transactionManager->wrap(function() use ($storyID, $slideIDs) {
                StorySlide::deleteAllLinkSlides($storyID);
                foreach ($slideIDs as $slideID) {
                    $slideLinkModel = StorySlide::createSlideLink($storyID, $slideID);
                    if (!$slideLinkModel->save()) {
                        throw new \Exception('Can\'t be saved StorySlide model. Errors: '. implode(', ', $slideLinkModel->getFirstErrors()));
                    }
                }
            });
        }
    }

    public function haveStory(): bool
    {
        return ($this->model->story !== null);
    }

    public function getModel(): StudyTask
    {
        return $this->model;
    }

    public function getStorySlides(): array
    {
        if (($storyModel = $this->model->story) === null) {
            return [];
        }
        return SelectSlideWidgetHelper::getSlides($storyModel->getStorySlidesWidgetSelected());
    }

    public function getStoryID(): ?int
    {
        if (($storyModel = $this->model->story) === null) {
            return null;
        }
        return $storyModel->id;
    }
}
