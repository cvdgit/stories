<?php

namespace api\modules\v1\controllers;

use backend\components\course\builder\course\ApiLessonModifier;
use backend\components\course\builder\course\CourseLessonBuilder;
use backend\components\course\builder\ApiCourseBuilder;
use common\components\StoryCover;
use common\helpers\Url;
use common\models\StoryTest;
use common\services\QuizService;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\models\Story as Course;

class CourseController extends Controller
{

    private $quizService;

    public function __construct($id, $module, QuizService $quizService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->quizService = $quizService;
    }

    /**
     * @throws NotFoundHttpException
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $this->response->format = Response::FORMAT_JSON;

        if (($courseModel = Course::findOne($id)) === null) {
            throw new NotFoundHttpException('Course not found');
        }

        if (count($courseModel->lessons) === 0) {
            throw new HttpException(500, 'Разделы не найдены');
        }

        $builder = new CourseLessonBuilder();
        $apiBuilder = new ApiLessonModifier($builder->build($courseModel->lessons), new ApiCourseBuilder(), $this->quizService);
        $apiResult = $apiBuilder->build();

        $coverImage = null;
        if (!empty($courseModel->cover)) {
            $coverImage = [
                'url' => Url::homeUrl() . StoryCover::getStoryThumbPath($courseModel->cover),
            ];
        }

        return [
            'course' => [
                'title' => $courseModel->title,
                'description' => $courseModel->description,
                'id' => $courseModel->id,
                'lessons' => $apiResult->getItems(),
                'coverImage' => $coverImage,
            ],
            'links' => $apiResult->getLinks(),
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
