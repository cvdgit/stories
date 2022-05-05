<?php

namespace backend\controllers;

use backend\models\audio_file\AudioFileSearch;
use backend\models\audio_file\CreateAudioFileModel;
use backend\models\audio_file\UpdateAudioFileModel;
use backend\services\AudioFileService;
use common\models\AudioFile;
use common\rbac\UserRoles;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\UploadedFile;

class AudioFileController extends Controller
{

    private $audioFileService;

    public function __construct($id, $module, AudioFileService $audioFileService, $config = [])
    {
        $this->audioFileService = $audioFileService;
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_TEST],
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

    public function actionIndex(Request $request): string
    {
        $searchModel = new AudioFileSearch();
        $dataProvider = $searchModel->search($request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id): Response
    {
        if (($audioFileModel = AudioFile::findOne($id)) === null) {
            throw new NotFoundHttpException('Audio file model not found');
        }
        try {
            $audioFileModel->delete();
            Yii::$app->session->setFlash('success', 'Запись успешно удалена');
        }
        catch (\Exception $ex) {
            Yii::$app->session->setFlash('error', $ex->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * @throws NotFoundHttpException
     * @return Response|string
     */
    public function actionUpdate(int $id)
    {
        if (($audioFileModel = AudioFile::findOne($id)) === null) {
            throw new NotFoundHttpException('Audio file model not found');
        }

        $form = new UpdateAudioFileModel($audioFileModel);
        if ($form->load($this->request->post())) {
            $form->audio_file = UploadedFile::getInstance($form, 'audio_file');
            try {
                $this->audioFileService->updateAudioFile($audioFileModel, $form);
                Yii::$app->session->setFlash('success', 'Запись успешно сохранена');
            }
            catch (\Exception $ex) {
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
            return $this->refresh();
        }

        return $this->render('update', [
            'model' => $form,
        ]);
    }

    public function actionCreateAudioFile(): array
    {
        $this->response->format = Response::FORMAT_JSON;
        $model = new CreateAudioFileModel();
        if ($model->load($this->request->post())) {
            $model->audio_file = UploadedFile::getInstance($model, 'audio_file');
            try {
                $fileName = $model->uploadAudioFile();
                $audioFile = $model->createAudioFile($fileName);
                return ['success' => true, 'audio_file_id' => $audioFile->id, 'audio_file_name' => $audioFile->name];
            }
            catch (\Exception $ex) {
                return ['success' => false, 'message' => $ex->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'no data'];
    }
}
