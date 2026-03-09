<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\widgets\StudentRequiredStories;

use Exception;
use modules\edu\RequiredStory\RequiredStoriesService;
use yii\base\Widget;

class StudentRequiredStoriesWidget extends Widget
{
    /** @var int */
    public $studentId;

    /**
     * @var RequiredStoriesService
     */
    private $requiredStoriesService;

    public function __construct(RequiredStoriesService $requiredStoriesService, $config = [])
    {
        parent::__construct($config);
        $this->requiredStoriesService = $requiredStoriesService;
    }

    /**
     * @throws Exception
     */
    public function run(): string
    {
        return $this->render('student-required-stories', [
            'dataProvider' => $this->requiredStoriesService->fetchForStudentWidget($this->studentId),
        ]);
    }
}
