<?php

declare(strict_types=1);

namespace modules\edu\widgets;

use common\models\Story;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduStudent;
use modules\edu\query\EduProgramStoriesFetcher;
use modules\edu\query\StudentQuestionFetcher;
use modules\edu\query\StudentStatsFetcher;
use modules\edu\query\StudentStoryDurationFetcher;
use modules\edu\query\StudentStoryStatByDateFetcher;
use yii\base\Widget;

class StudentStatWidget extends Widget
{
    /** @var EduClassProgram */
    public $classProgram;

    /** @var int */
    public $classId;

    /** @var EduStudent */
    public $student;

    /** @var StudentStoryDurationFetcher */
    private $studentStoryDurationFetcher;

    public function __construct(StudentStoryDurationFetcher $studentStoryDurationFetcher, $config = [])
    {
        parent::__construct($config);
        $this->studentStoryDurationFetcher = $studentStoryDurationFetcher;
    }

    public function run(): string
    {
        $programStoriesData = (new EduProgramStoriesFetcher())->fetch($this->classId, $this->classProgram->program_id);
        $storyIds = array_column($programStoriesData, 'storyId');

        $storyModels = Story::find()
            ->where(['in', 'id', $storyIds])
            ->indexBy('id')
            ->all();

        $statData = (new StudentStoryStatByDateFetcher())->fetch($this->student->id, $storyIds);
        $stat = (new StudentStatsFetcher())->fetch($statData, $programStoriesData);

        return $this->render('stat', [
            'classProgram' => $this->classProgram,
            'student' => $this->student,
            'stat' => $stat,
            'storyModels' => $storyModels,
            'questionFetcher' => new StudentQuestionFetcher(),
            'timeFetcher' => $this->studentStoryDurationFetcher,
        ]);
    }
}
