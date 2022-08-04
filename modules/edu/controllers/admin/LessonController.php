<?php

namespace modules\edu\controllers\admin;

use modules\edu\forms\admin\SelectStoryForm;
use modules\edu\models\EduLesson;
use modules\edu\services\LessonService;
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

    public function actionCreate(int $topic_id)
    {
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
        ]);
    }

    /**
     * Updates an existing EduLesson model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $storiesDataProvider = new ActiveDataProvider([
            'query' => $model->getStories()
        ]);

        return $this->render('update', [
            'model' => $model,
            'storiesDataProvider' => $storiesDataProvider,
        ]);
    }

    /**
     * Deletes an existing EduLesson model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
            catch (\Exception $exception) {
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return $this->renderAjax('_select_story_dialog', [
            'model' => $selectStoryForm,
        ]);
    }
}
