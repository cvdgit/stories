<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\widgets\StudentRequiredStories;

use DateTimeImmutable;
use Exception;
use modules\edu\RequiredStory\RequiredStoriesService;
use yii\base\Widget;

class StudentRequiredStoriesWidget extends Widget
{
    /** @var int */
    public $studentId;
    /** @var DateTimeImmutable */
    public $startDate;

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
        $dataProvider = $this->requiredStoriesService->fetchForStudentWidget(
            $this->studentId,
            $this->startDate
        );

        $title = 'Обязательные истории на сегодня';
        if (count($dataProvider->getModels()) === 0) {
            $title = 'Обязательные истории на завтра';
            $dataProvider = $this->requiredStoriesService->fetchForStudentWidget(
                $this->studentId,
                $this->startDate->modify('+1day')
            );
        }

        $havePriorityStories = count(
                array_filter(
                    $dataProvider->getModels(),
                    static function (WidgetRequiredStory $model) {
                        $session = $model->getSession();
                        if ($session === null) {
                            return $model->isPriority();
                        }
                        return $model->isPriority() && $session->isCompleted() === false;
                    },
                ),
            ) > 0;

        return $this->render('student-required-stories', [
            'dataProvider' => $dataProvider,
            'title' => $title,
            'havePriorityStories' => $havePriorityStories,
        ]);
    }
}
