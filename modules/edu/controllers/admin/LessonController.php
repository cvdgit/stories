<?php

namespace modules\edu\controllers\admin;

use Exception;
use modules\edu\forms\admin\LessonStoryOrderForm;
use modules\edu\forms\admin\SelectStoryForm;
use modules\edu\models\EduLesson;
use modules\edu\models\EduTopic;
use modules\edu\services\LessonService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * LessonController implements the CRUD actions for EduLesson model.
 */
class LessonController extends Controller
{

    private $lessonService;

    public function __construct($id, $module, LessonService $lessonService, $config = [])
    {
        $this->lessonService = $lessonService;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCreate(int $topic_id)
    {
        if (($topic = EduTopic::findOne($topic_id)) === null) {
            throw new NotFoundHttpException('Тема не найдена');
        }

        $model = new EduLesson([
            'topic_id' => $topic_id,
        ]);

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['update', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'topicModel' => $topic,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', 'Изменения успешно сохранены');
            return $this->refresh();
        }

        $storiesDataProvider = new ActiveDataProvider([
            'query' => $model->getStories()
        ]);

        return $this->render('update', [
            'model' => $model,
            'storiesDataProvider' => $storiesDataProvider,
        ]);
    }

    public function actionDelete($id)
    {
        $this->response->format = Response::FORMAT_JSON;
        $this->lessonService->delete($id);
        return ['success' => true];
    }

    /**
     * Finds the EduLesson model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return EduLesson the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EduLesson::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionSelectStory(int $lesson_id)
    {
        if (($lessonModel = EduLesson::findOne($lesson_id)) === null) {
            throw new NotFoundHttpException('Урок не найден');
        }
        $selectStoryForm = new SelectStoryForm();
        if ($this->request->isPost && $selectStoryForm->load($this->request->post())) {
            $this->response->format = Response::FORMAT_JSON;
            try {
                $this->lessonService->addStory($lessonModel, $selectStoryForm);
                return ['success' => true];
            }
            catch (Exception $exception) {
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return $this->renderAjax('_select_story_dialog', [
            'model' => $selectStoryForm,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionOrder(int $lesson_id): array
    {
        $this->response->format = Response::FORMAT_JSON;
        if (($lessonModel = EduLesson::findOne($lesson_id)) === null) {
            throw new NotFoundHttpException('Урок не найден');
        }
        $form = new LessonStoryOrderForm();
        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            try {
                $this->lessonService->saveOrder($lessonModel->id, $form);
                return ['success' => true];
            }
            catch (Exception $exception) {
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ['success' => true];
    }

    public function actionDeleteStory(int $lesson_id, int $story_id): array
    {
        $this->response->format = Response::FORMAT_JSON;
        $this->lessonService->deleteStory($lesson_id, $story_id);
        return ['success' => true];
    }
}
