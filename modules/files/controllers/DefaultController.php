<?php

namespace modules\files\controllers;

use Exception;
use modules\files\services\StudyFileService;
use modules\files\models\StudyFile;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `files` module
 */
class DefaultController extends Controller
{

    private $studyFileService;

    public function __construct($id, $module, StudyFileService $studyFileService, $config = [])
    {
        $this->studyFileService = $studyFileService;
        parent::__construct($id, $module, $config);
    }

    public function actionGet(string $id)
    {
        try {

            if (($fileModel = StudyFile::findForDownload($id)) === null) {
                throw new NotFoundHttpException('Файл не найден');
            }

            $filePath = $fileModel->getFilePath();
            if (!file_exists($filePath)) {
                throw new NotFoundHttpException('Файл не найден');
            }

            try {
                $this->studyFileService->addOpenHistory(Yii::$app->user->id, $fileModel->id);
            }
            catch(Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
            }

            Yii::$app->response->sendFile($filePath, $fileModel->getNameWithExtension());
        }
        catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
            Yii::$app->session->setFlash('error', $ex->getMessage());
            return $this->goBack();
        }
    }
}
