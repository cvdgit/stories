<?php

namespace modules\edu\controllers\admin;

use Exception;
use modules\edu\forms\admin\TopicLessonOrderForm;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduTopic;
use modules\edu\models\EduTopicSearch;
use modules\edu\services\TopicService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TopicController implements the CRUD actions for EduTopic model.
 */
class TopicController extends Controller
{

    private $topicService;

    public function __construct($id, $module, TopicService $topicService, $config = [])
    {
        $this->topicService = $topicService;
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
     * Lists all EduTopic models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new EduTopicSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCreate(int $class_program_id)
    {
        if (($classProgram = EduClassProgram::findOne($class_program_id)) === null) {
            throw new NotFoundHttpException('Программа обучения не найдена');
        }

        $model = new EduTopic([
            'class_program_id' => $class_program_id,
        ]);

        if ($this->request->isPost && $model->load($this->request->post())) {

            $model->order = (new Query())
                    ->from('edu_topic')
                    ->where(['class_program_id' => $class_program_id])
                    ->max('`order`') + 1;

            if ($model->save()) {
                return $this->redirect(['update', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'classProgram' => $classProgram,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', 'Изменения успешно сохранены');
            return $this->refresh();
        }

        $lessonsDataProvider = new ActiveDataProvider([
            'query' => $model->getEduLessons(),
            'pagination' => false,
        ]);

        return $this->render('update', [
            'topicModel' => $model,
            'lessonsDataProvider' => $lessonsDataProvider,
        ]);
    }

    public function actionDelete(int $id)
    {
        $this->response->format = Response::FORMAT_JSON;
        $this->topicService->delete($id);
        return ['success' => true];
    }

    /**
     * Finds the EduTopic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return EduTopic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EduTopic::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionOrder(int $topic_id): array
    {
        $this->response->format = Response::FORMAT_JSON;

        $topicModel = $this->findModel($topic_id);

        $form = new TopicLessonOrderForm();
        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            try {
                $this->topicService->saveOrder($topicModel->id, $form);
                return ['success' => true];
            }
            catch (Exception $exception) {
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ['success' => true];
    }
}
