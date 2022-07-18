<?php

namespace modules\files\controllers;

use modules\files\forms\StudyFileSearch;
use common\rbac\UserRoles;
use modules\files\forms\CreateStudyFileForm;
use modules\files\forms\UpdateStudyFileForm;
use modules\files\models\StudyFile;
use modules\files\services\StudyFileService;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * StudyFileController implements the CRUD actions for StudyFile model.
 */
class StudyFileController extends Controller
{

    private $studyFileService;

    public function __construct($id, $module, StudyFileService $studyFileService, $config = [])
    {
        $this->studyFileService = $studyFileService;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::ROLE_ADMIN],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel = new StudyFileSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $createFileForm = new CreateStudyFileForm();
        if ($this->request->isPost && $createFileForm->load($this->request->post())) {
            try {
                $createFileForm->file = UploadedFile::getInstance($createFileForm, 'file');
                $fileId = $this->studyFileService->create($createFileForm);
                Yii::$app->session->setFlash('success', 'Файл успешно создан');
                return $this->redirect(['study-file/update', 'id' => $fileId]);
            }
            catch (\Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                Yii::$app->session->setFlash('error', $exception->getMessage());
                return $this->refresh();
            }
        }
        return $this->render('create', [
            'model' => $createFileForm,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $fileModel = $this->findModel($id);
        $updateFileForm = new UpdateStudyFileForm($fileModel);
        if ($this->request->isPost && $updateFileForm->load($this->request->post())) {
            try {
                $updateFileForm->file = UploadedFile::getInstance($updateFileForm, 'file');
                $this->studyFileService->update($fileModel, $updateFileForm);
                Yii::$app->session->setFlash('success', 'Файл успешно изменен');
                return $this->refresh();
            }
            catch (\Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                Yii::$app->session->setFlash('error', $exception->getMessage());
                return $this->refresh();
            }
        }
        return $this->render('update', [
            'fileModel' => $fileModel,
            'model' => $updateFileForm,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $fileModel = $this->findModel($id);
        $fileModel->delete();
        return $this->redirect(['index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): StudyFile
    {
        if (($model = StudyFile::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Файл не найден');
    }
}
