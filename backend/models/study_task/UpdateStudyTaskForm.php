<?php

namespace backend\models\study_task;

use backend\helpers\SelectSlideWidgetHelper;
use common\models\StorySlide;
use common\models\StudyTask;
use yii\helpers\ArrayHelper;

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

    public static function arrayDifference(array $array1, array $array2, array $keysToCompare = null)
    {
        $serialize = function (&$item, $idx, $keysToCompare) {
            if (is_array($item) && $keysToCompare) {
                $a = array();
                foreach ($keysToCompare as $k) {
                    if (array_key_exists($k, $item)) {
                        $a[$k] = $item[$k];
                    }
                }
                $item = $a;
            }
            $item = serialize($item);
        };

        $deserialize = function (&$item) {
            $item = unserialize($item);
        };

        array_walk($array1, $serialize, $keysToCompare);
        array_walk($array2, $serialize, $keysToCompare);

        // Items that are in the original array but not the new one
        $deletions = array_diff($array1, $array2);
        $insertions = array_diff($array2, $array1);

        array_walk($insertions, $deserialize);
        array_walk($deletions, $deserialize);

        return array('insertions' => $insertions, 'deletions' => $deletions);
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

            $storyModel = $this->model->story;
            $storySlides = array_map(static function(StorySlide $slideModel) {
                return $slideModel->id;
            }, $storyModel->getStorySlidesWidgetSelected());

            $selectedSlides = array_map(static function($slideID) {
                return (int) $slideID;
            }, $this->slide_ids);

            ['insertions' => $insertions, 'deletions' => $deletions] = self::arrayDifference($storySlides, $selectedSlides);

            $storyID = $storyModel->id;
            $this->transactionManager->wrap(function() use ($storyID, $deletions, $insertions) {

                StorySlide::deleteAllFinalSlides($storyID);

                foreach ($deletions as $deleteSlideId) {
                    StorySlide::deleteSlide($deleteSlideId);
                }

                foreach ($insertions as $insertSlideId) {

                    $slideModel = StorySlide::findSlide($insertSlideId);
                    $slideId = $slideModel->id;
                    if ($slideModel->isLink()) {
                        $slideId = $slideModel->link_slide_id;
                    }

                    $this->createSlide($storyID, $slideId);
                }

                $this->createFinalSlide($storyID);
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
