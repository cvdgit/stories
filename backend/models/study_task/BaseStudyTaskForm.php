<?php

namespace backend\models\study_task;

use backend\components\SlideWrapper;
use backend\components\story\HTMLBLock;
use backend\components\StudyTaskFinalSlide;
use backend\models\editor\QuestionForm;
use backend\services\StoryEditorService;
use common\models\slide\SlideKind;
use common\models\slide\SlideStatus;
use common\models\Story;
use common\models\StorySlide;
use common\models\study_task\StudyTaskStatus;
use common\services\TransactionManager;
use DomainException;
use Yii;
use yii\base\Model;

class BaseStudyTaskForm extends Model
{

    public $title;
    public $description;
    public $status;
    public $slide_ids = [];
    public $story_id;

    protected $transactionManager;
    private $editorService;

    public function __construct($config = [])
    {
        $this->transactionManager = Yii::createObject(TransactionManager::class);
        $this->editorService = Yii::createObject(StoryEditorService::class);
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['description'], 'string'],
            [['status', 'story_id'], 'integer'],
            ['status', 'default', 'value' => 0],
            [['title'], 'string', 'max' => 255],
            ['slide_ids', 'each', 'rule' => ['integer']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Название',
            'description' => 'Описание',
            'status' => 'Статус',
            'story_id' => 'История',
        ];
    }

    public function getStudyTaskStatusesAsArray(): array
    {
        return StudyTaskStatus::asArray();
    }

    public function isNewRecord(): bool
    {
        return $this instanceof CreateStudyTaskForm;
    }

    public function getStorySlides(): array
    {
        return [];
    }

    public function haveStory(): bool
    {
        return !empty($this->story_id);
    }

    public function createSlide(int $storyID, int $slideID): void
    {
        $slideModel = StorySlide::findSlide($slideID);
        $slideWrapper = new SlideWrapper($slideModel->data);

        if (($testId = $slideWrapper->findTestId()) !== null) {
            // Если на сайде есть тест
            $newSlideModel = StorySlide::createSlideFull($storyID, (new SlideWrapper())->getSlideHtml(), null, SlideStatus::VISIBLE, SlideKind::FINAL_SLIDE);
            if (!$newSlideModel->save()) {
                throw new DomainException('Can\'t be saved StorySlide model. Errors: ' . implode(', ', $newSlideModel->getFirstErrors()));
            }
            $testForm = new QuestionForm();
            $testForm->story_id = $storyID;
            $testForm->slide_id = $newSlideModel->id;
            $testForm->test_id = $testId;
            $testForm->required = 1;
            $this->editorService->createBlock($newSlideModel, $testForm, HTMLBLock::class);
            $testForm->afterCreate($newSlideModel);
        }
        else {
            // Если теста нет, то создаем ссылку на слайд
            $linkSlideModel = StorySlide::createSlideLink($storyID, $slideID);
            if (!$linkSlideModel->save()) {
                throw new DomainException('Can\'t be saved StorySlide model. Errors: ' . implode(', ', $linkSlideModel->getFirstErrors()));
            }
        }
    }

    public function createFinalSlide(int $storyID): void
    {
        $html = StudyTaskFinalSlide::create();
        $finalSlide = StorySlide::createSlideFull($storyID, $html, null, SlideStatus::VISIBLE, SlideKind::FINAL_SLIDE);
        $finalSlide->save();
    }

    public function getStory(): ?Story
    {
        return null;
    }
}
