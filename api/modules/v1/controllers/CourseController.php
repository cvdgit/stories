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

    private function processQuiz($data)
    {

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
        $slideLinks = [];
        foreach ($slides as $slide) {

            $slideData = StorySlide::getSlideData($slide);

            $data = (new SlideModifier($slide->id, $slideData))
                ->addImageUrl()
                ->addVideoUrl()
                ->forLesson();

            $slideItems = $data['blocks'];
            $slideLinks = array_merge($slideLinks, $data['links']);

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
                            'settings' => [
                                'passToContinue' => false,
                            ],
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

/*        if (count($lessons) > 0) {
            $lessons[] = [
                'id' => $lessonIndex,
                'title' => 'Конец',
                'type' => 'blocks',
                'items' => [
                    [
                        'id' => $lessonIndex,
                        'type' => 'text',
                        'items' => [
                            [
                                'id' => $lessonIndex,
                                'paragraph' => 'Курс пройден',
                            ]
                        ],
                    ],
                ],
            ];
        }*/

        foreach ($slideLinks as $i => $slideLink) {

            $alias = $slideLink['alias'];
            $number = $slideLink['number'];

            if (($storyModel = Story::findOne(['alias' => $alias])) !== null) {
                if (($slideModel = StorySlide::findSlideByNumber($storyModel->id, $number)) !== null) {

                    $slideData = StorySlide::getSlideData($slideModel);
                    $data = (new SlideModifier($storyModel->id, $slideData))
                        ->addImageUrl()
                        ->addVideoUrl()
                        ->forLesson();

                    $slideItems = $data['blocks'];
                    unset($slideLinks[$i]['alias'], $slideLinks[$i]['number']);
                    $slideLinks[$i]['items'] = $slideItems;
                }
            }
        }

        return [
            'course' => [
                'title' => $course->title,
                'description' => $course->description,
                'id' => $course->id,
                'lessons' => $lessons,
            ],
            'links' => $slideLinks,
        ];
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