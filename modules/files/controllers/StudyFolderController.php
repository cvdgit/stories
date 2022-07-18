<?php

namespace modules\files\controllers;

use common\rbac\UserRoles;
use modules\files\forms\FilesUploadForm;
use modules\files\forms\StudyFolderSearch;
use modules\files\models\StudyFolder;
use modules\files\services\StudyFileService;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * StudyFolderController implements the CRUD actions for StudyFolder model.
 */
class StudyFolderController extends Controller
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
                    'upload-files' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel = new StudyFolderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new StudyFolder();
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
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);
        $filesUploadForm = new FilesUploadForm();
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->render('update', [
            'model' => $model,
            'uploadModel' => $filesUploadForm,
            'files' => $model->getStudyFiles()->orderBy(['name' => SORT_ASC])->all(),
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUploadFiles(int $id): Response
    {
        $folderModel = $this->findModel($id);
        $filesUploadForm = new FilesUploadForm();
        if ($this->request->isPost && $filesUploadForm->load($this->request->post())) {
            $filesUploadForm->files = UploadedFile::getInstances($filesUploadForm, 'files');
            try {
                $this->studyFileService->uploadFiles($folderModel, $filesUploadForm);
                Yii::$app->session->setFlash('success', 'Файлы успешно загружены');
            }
            catch (\Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                Yii::$app->session->setFlash('error', $exception->getMessage());
            }
        }
        return $this->redirect(['study-folder/update', 'id' => $id]);
    }

    /**
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): StudyFolder
    {
        if (($model = StudyFolder::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
