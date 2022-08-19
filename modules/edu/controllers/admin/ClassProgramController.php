<?php

namespace modules\edu\controllers\admin;

use Exception;
use modules\edu\forms\admin\ClassProgramTopicOrderForm;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduClassProgramSearch;
use modules\edu\services\ClassProgramService;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ClassProgramController implements the CRUD actions for EduClassProgram model.
 */
class ClassProgramController extends Controller
{

    private $classProgramService;

    public function __construct($id, $module, ClassProgramService $classProgramService, $config = [])
    {
        $this->classProgramService = $classProgramService;
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
     * Lists all EduClassProgram models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new EduClassProgramSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new EduClassProgram model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new EduClassProgram();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['index']);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EduClassProgram model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->refresh();
        }

        $topicsDataProvider = new ActiveDataProvider([
            'query' => $model->getEduTopics(),
        ]);

        return $this->render('update', [
            'model' => $model,
            'topicsDataProvider' => $topicsDataProvider,
        ]);
    }

    /**
     * Deletes an existing EduClassProgram model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EduClassProgram model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return EduClassProgram the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EduClassProgram::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionOrder(int $class_program_id): array
    {
        $this->response->format = Response::FORMAT_JSON;

        $classProgram = $this->findModel($class_program_id);

        $form = new ClassProgramTopicOrderForm();
        if ($this->request->isPost && $form->load($this->request->post(), '')) {
            try {
                $this->classProgramService->saveOrder($classProgram->id, $form);
                return ['success' => true];
            }
            catch (Exception $exception) {
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ['success' => true];
    }
}
