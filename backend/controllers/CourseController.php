<?php

namespace backend\controllers;

use backend\components\course\builder\course\CourseLessonBuilder;
use backend\components\course\builder\LessonBuilder;
use backend\components\course\builder\slides\SlidesLessonBuilder;
use backend\components\course\LessonCreateForm;
use backend\components\course\LessonDeleteForm;
use backend\components\course\LessonNameForm;
use backend\components\course\LessonOrderForm;
use backend\components\course\LessonService;
use backend\components\SlideWrapper;
use backend\models\editor\QuestionForm;
use backend\services\StoryEditorService;
use common\components\JsonResponse;
use common\models\Lesson;
use common\models\Story;
use common\models\StorySlide;
use common\models\StoryTest;
use yii\base\Model;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CourseController extends Controller
{

    public $layout = 'course';

    private $lessonService;
    private $editorService;

    public function __construct($id, $module, LessonService $lessonService, StoryEditorService $editorService, $config = [])
    {
        $this->lessonService = $lessonService;
        $this->editorService = $editorService;
        parent::__construct($id, $module, $config);
    }

    public function actionUpdate(int $id): string
    {
        $storyModel = $this->findModel($id);

        $builder = new CourseLessonBuilder();
        $lessons = $builder->build($storyModel->lessons)->getLessons();

        $course = [
            'story_id' => $storyModel->id,
            'lessons' => $lessons,
        ];

        return $this->render('update', [
            'storyModel' => $storyModel,
            'course' => Json::encode($course),
            'haveSlides' => count($storyModel->storySlides) > 0,
            'haveLessons' => count($lessons) > 0,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findModel($id): ?Story
    {
        if (($model = Story::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDelete(int $id): Response
    {
        $this->lessonService->deleteLessons($id);
        return $this->redirect(['update', 'id' => $id]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionCreateFromSlides(int $id): Response
    {
        $storyModel = $this->findModel($id);
        $builder = new SlidesLessonBuilder();
        $collection = $builder->build($storyModel->storySlides);
        $this->lessonService->saveLessonCollection($storyModel->id, $collection);
        return $this->redirect(['update', 'id' => $id]);
    }

    public function actionSave(): array
    {
        $this->response->format = Response::FORMAT_JSON;
        if ($this->request->isPost) {

            $coursePost = $this->request->post('course');

            try {
                $this->lessonService->saveLessons($coursePost['story_id'], $coursePost['lessons']);
                return (new JsonResponse())->success()->asArray();
            }
            catch (\Exception $ex) {
                return (new JsonResponse())
                    ->success(false)
                    ->message($ex->getMessage())
                    ->asArray();
            }
        }
        return (new JsonResponse())->success(false)->asArray();
    }

    public function actionLessonsUpdate(): array
    {
        $this->response->format = Response::FORMAT_JSON;
        if ($this->request->isPost) {

            $coursePost = $this->request->post('course');

            try {
                $this->lessonService->updateLessons($coursePost['lessons']);
                return (new JsonResponse())->success()->asArray();
            }
            catch (\Exception $ex) {
                return (new JsonResponse())
                    ->success(false)
                    ->message($ex->getMessage())
                    ->asArray();
            }
        }
        return (new JsonResponse())->success(false)->asArray();
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionQuizCreateForm(string $lesson_uuid): string
    {
        if (($lessonModel = Lesson::findOneByUUID($lesson_uuid)) === null) {
            throw new NotFoundHttpException('Lesson not found');
        }

        $storyId = $lessonModel->story_id;
        $form = new QuestionForm();
        $form->story_id = $storyId;
        $form->lesson_id = $lessonModel->id;

        return $this->renderAjax('_quiz_form', [
            'action' => ['course/quiz-create'],
            'model' => $form,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionQuizUpdateForm(int $slide_id, int $lesson_id): string
    {
        if (($slideModel = StorySlide::findOne($slide_id)) === null) {
            throw new NotFoundHttpException('Slide not found');
        }
        if (($lessonModel = Lesson::findOne($lesson_id)) === null) {
            throw new NotFoundHttpException('Lesson not found');
        }

        $slideWrapper = new SlideWrapper($slideModel->getSlideOrLinkData());

        if (($block = $slideWrapper->getQuizBlock()) === null) {
            throw new NotFoundHttpException('Slide block not found');
        }

        $form = new QuestionForm(['scenario' => 'update']);
        $form->slide_id = $slideModel->id;
        $form->block_id = $block->getId();
        $form->story_id = $slideModel->story_id;
        $form->lesson_id = $lessonModel->id;

        $values = $block->getValues();
        $form->load($values, '');

        return $this->renderAjax('_quiz_form', [
            'action' => ['course/quiz-update'],
            'model' => $form,
        ]);
    }

    public function actionQuizCreate(): array
    {
        $this->response->format = Response::FORMAT_JSON;
        $form = new QuestionForm();
        if ($form->load($this->request->post()) && $form->validate()) {

            try {

                $lessonModel = Lesson::findOne($form->lesson_id);
                $lessonModel->updateTypeQuiz();

                $slideId = $this->editorService->createSlide($form->story_id, $this->lessonService->findQuizSlideId($lessonModel));
                $slideModel = StorySlide::findOne($slideId);

                $quizModel = StoryTest::findOne($form->test_id);
                $lessonModel->createQuizBlock($slideModel->id, $quizModel->id);

                $this->editorService->createQuizBlock($slideModel, $form);
                $form->afterCreate($slideModel);

                return [
                    'success' => true,
                    'slide_id' => $slideModel->id,
                    'block_id' => $form->block_id,
                    'quiz_id' => $quizModel->id,
                    'quiz_name' => $quizModel->title,
                ];
            }
            catch(\Exception $ex) {
                return ['success' => false, 'errors' => $ex->getMessage()];
            }
        }
        return ['success' => false, 'errors' => implode('<br/>', $form->getErrorSummary(true))];
    }

    public function actionQuizUpdate(): array
    {
        $this->response->format = Response::FORMAT_JSON;
        $form = new QuestionForm(['scenario' => 'update']);
        if ($form->load($this->request->post()) && $form->validate()) {
            $slideModel = StorySlide::findOne($form->slide_id);
            $this->editorService->updateBlock($form);
            $form->afterUpdate($slideModel);
            $quizModel = StoryTest::findOne($form->test_id);
            $this->lessonService->updateLessonQuizId($form->lesson_id, $slideModel->id, $quizModel->id);
            return [
                'success' => true,
                'block_id' => $form->block_id,
                'quiz_id' => $quizModel->id,
                'quiz_name' => $quizModel->title,
            ];
        }
        return $form->getErrors();
    }

    public function actionLessonDelete(): array
    {
        $this->response->format = Response::FORMAT_JSON;
        $form = new LessonDeleteForm();
        if ($form->load($this->request->post(), '')) {
            try {
                $this->lessonService->deleteLesson($form);
                return ['success' => true];
            }
            catch (\Exception $ex) {
                return ['success' => false, 'message' => $ex->getMessage()];
            }
        }
        return ['success' => false];
    }

    public function actionLessonCreate(): array
    {
        $this->response->format = Response::FORMAT_JSON;
        $form = new LessonCreateForm();
        if ($form->load($this->request->post(), '')) {
            try {

                $lessonModel = $this->lessonService->createLesson($form);
                $lesson = (new LessonBuilder())
                    ->createBlocksLesson(
                        $lessonModel->uuid,
                        $lessonModel->name,
                        $lessonModel->order,
                        $lessonModel->id
                    );

                return ['success' => true, 'lesson' => $lesson];
            }
            catch (\Exception $ex) {
                return ['success' => false, 'message' => $ex->getMessage()];
            }
        }
        return ['success' => false];
    }

    public function actionUpdateLessonsOrder(): array
    {
        $this->response->format = Response::FORMAT_JSON;

        $data = $this->request->post();
        $models = [];
        foreach ($data as $i => $dataRow) {
            $models[$i] = new LessonOrderForm();
        }

        if (Model::loadMultiple($models, $data, '') && Model::validateMultiple($models)) {
            try {
                $this->lessonService->updateLessonsOrder($models);
                return ['success' => true];
            }
            catch (\Exception $ex) {
                return ['success' => false, 'message' => $ex->getMessage()];
            }
        }

        return ['success' => false];
    }

    public function actionUpdateLessonName(): array
    {
        $this->response->format = Response::FORMAT_JSON;
        $form = new LessonNameForm();
        if ($form->load($this->request->post(), '')) {
            try {
                $this->lessonService->updateLessonName($form);
                return ['success' => true];
            }
            catch (\Exception $ex) {
                return ['success' => false, 'message' => $ex->getMessage()];
            }
        }
        return ['success' => false];
    }
}
