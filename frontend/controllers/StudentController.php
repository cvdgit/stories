<?php

namespace frontend\controllers;

use common\models\User;
use common\models\UserStudent;
use Exception;
use frontend\components\UserController;
use frontend\models\UserStudentForm;
use frontend\services\StudentService;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class StudentController extends UserController
{

    private $studentService;

    public function __construct($id, $module, StudentService $studentService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->studentService = $studentService;
    }

/*    public function actionCreate()
    {
        $currentUser = Yii::$app->user->identity;

        $studentForm = new UserStudentForm();
        if ($this->request->isPost && $studentForm->load($this->request->post())) {
            $this->response->format = Response::FORMAT_JSON;

            try {
                $this->studentService->create($currentUser->id, $studentForm);
                return ['success' => true, 'students' => $currentUser->getStudentsAsArray()];
            }
            catch (Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                return ['success' => false, 'message' => 'При создании ученика произошла ошибка'];
            }
        }
        return $this->renderAjax('create', [
            'model' => $studentForm,
        ]);
    }*/

/*    public function actionDelete(int $id): array
    {
        $this->response->format = Response::FORMAT_JSON;
        $currentUser = Yii::$app->user->identity;
        try {
            $this->studentService->delete($id, $currentUser->id);
            return ['success' => true];
        }
        catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ['success' => false];
        }
    }*/

    /**
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    /*public function actionUpdate(int $id)
    {
        if (($studentModel = UserStudent::findOne($id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $currentUser = Yii::$app->user->identity;

        if (!$this->studentService->isOwnerThisUser($studentModel, $currentUser->id)) {
            throw new ForbiddenHttpException('Отказано в доступе');
        }

        $studentForm = new UserStudentForm($studentModel);
        if ($this->request->isPost && $studentForm->load($this->request->post())) {
            $this->response->format = Response::FORMAT_JSON;

            try {
                $this->studentService->update($studentModel, $studentForm);
                return ['success' => true, 'students' => $currentUser->getStudentsAsArray()];
            }
            catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => 'При редактировании ученика произошла ошибка'];
            }
        }
        return $this->renderAjax('update', [
            'updateRoute' => ['student/update', 'id' => $studentModel->id],
            'model' => $studentForm,
        ]);
    }*/

/*    public function actionIndex(): string
    {
        $currentUser = Yii::$app->user->identity;
        return $this->render('index', [
            'students' => $currentUser->getStudentsAsArray(),
        ]);
    }*/

}
