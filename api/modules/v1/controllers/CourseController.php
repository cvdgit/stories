<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\Story;
use api\modules\v1\models\StorySlide;
use backend\components\SlideModifier;
use common\models\slide\SlideKind;
use common\models\StoryTest;
use common\services\QuizService;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CourseController extends Controller
{

    private $quizService;

    public function __construct($id, $module, QuizService $quizService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->quizService = $quizService;
    }

    public function actionView(int $id)
    {
        $this->response->format = Response::FORMAT_JSON;

        $course = Story::find()
            ->with('allSlides')
            ->where(['id' => $id])
            ->one();

        $lessons = [];

        $lessonIndex = 1;
        $dividerIndex = 1;
        $currentLesson = null;

        $slides = $course->allSlides;
        foreach ($slides as $slide) {

            $slideData = $slide->data;
            if (SlideKind::isLink($slide)) {
                $slideData = StorySlide::findOne($slide->link_slide_id)->data;
            }

            $search = [
                'data-id=""',
                'data-background-color="#000000"',
            ];
            $replace = [
                'data-id="' . $this->id . '"',
                'data-background-color="#fff"',
            ];
            $slideData = str_replace($search, $replace, $slideData);

            $slideItems = (new SlideModifier($slide->id, $slideData))
                ->addImageUrl()
                ->forLesson();

            $end = next($slides) === false;
            if (($currentLesson !== null && SlideKind::isQuiz($slide)) || $end) {
                if ($currentLesson && count($currentLesson['items']) > 0) {
                    $lessons[] = $currentLesson;
                }
                $currentLesson = null;
                $lessonIndex++;
            }

            if (SlideKind::isQuiz($slide) && count($slideItems) > 0) {
                $quizItem = $slideItems[0];
                $currentLesson = [
                    'id' => $quizItem['id'],
                    'title' => $quizItem['title'],
                    'description' => $quizItem['description'],
                    'type' => 'quiz',
                    'items' => [
                        [
                            'id' => $quizItem['id'],
                            'type' => 'quiz',
                            'items' => $this->getQuizData($quizItem['id']),
                        ],
                    ],
                ];
                $lessons[] = $currentLesson;
                $currentLesson = null;
            }
            else {

                if ($currentLesson === null) {
                    $currentLesson = [
                        'id' => $lessonIndex,
                        'title' => "Раздел $lessonIndex",
                        'type' => 'blocks',
                        'items' => [],
                    ];
                }

                if (count($slideItems) > 0) {
                    foreach ($slideItems as $item) {
                        $currentLesson['items'][] = $item;
                    }
                    $currentLesson['items'][] = [
                        'id' => $dividerIndex++,
                        'type' => 'divider',
                        'items' => [],
                    ];
                }
            }
        }

        foreach ($lessons as $key => $value) {
            $lastItem = end($value['items']);
            if ($lastItem['type'] === 'divider') {
                array_pop($lessons[$key]['items']);
            }
        }

        return ['course' => [
            'title' => $course->title,
            'description' => $course->description,
            'id' => $course->id,
            'lessons' => $lessons,
        ]];
    }

    private function getQuizData(int $quizId): array
    {
        $quizModel = $this->findTestModel($quizId);
        return $this->quizService->getQuizData($quizModel);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findTestModel(int $id): ?StoryTest
    {
        if (($model = StoryTest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}