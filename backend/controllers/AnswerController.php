<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\forms\TestingAnswerForm;
use backend\models\AnswerImageUploadForm;
use backend\services\AnswerService;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\rbac\UserRoles;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\UploadedFile;

class AnswerController extends Controller
{
    /** @var AnswerService */
    private $answerService;

    public function __construct($id, $module, AnswerService $answerService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->answerService = $answerService;
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

    /**
     * @throws NotFoundHttpException
     */
    public function actionCreate(int $question_id, Request $request, Response $response)
    {
        if (($question = StoryTestQuestion::findOne($question_id)) === null) {
            throw new NotFoundHttpException('Вопрос не найден');
        }

        $answerForm = new TestingAnswerForm();
        $answerImageForm = new AnswerImageUploadForm();

        if ($answerForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;

            if (!$answerForm->validate()) {
                return ['success' => false, 'message' => 'Ошибка валидации'];
            }

            $answerImageForm->answerImage = UploadedFile::getInstance($answerImageForm, 'answerImage');
            if (!$answerImageForm->validate()) {
                return ['success' => false, 'message' => 'Ошибка при загрузке файла'];
            }

            $folder = Yii::getAlias('@public') . '/test_images';

            try {
                $this->answerService->create($question->id, $answerForm, $folder, $answerImageForm->answerImage);
                return ['success' => true, 'message' => 'Ответ успешно создан'];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return $this->renderAjax('create', [
            'question' => $question,
            'formModel' => $answerForm,
            'answerImageModel' => $answerImageForm,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id, Request $request, Response $response)
    {
        if (($answer = StoryTestAnswer::findOne($id)) === null) {
            throw new NotFoundHttpException('Ответ не найден');
        }

        $answerForm = new TestingAnswerForm($answer);
        $answerImageForm = new AnswerImageUploadForm();

        if ($answerForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;

            if (!$answerForm->validate()) {
                return ['success' => false, 'message' => 'Ошибка валидации'];
            }

            $answerImageForm->answerImage = UploadedFile::getInstance($answerImageForm, 'answerImage');
            if (!$answerImageForm->validate()) {
                return ['success' => false, 'message' => 'Ошибка при загрузке файла'];
            }

            $folder = Yii::getAlias('@public') . '/test_images';

            try {
                $this->answerService->update($answer, $answerForm, $folder, $answerImageForm->answerImage);
                return ['success' => true, 'message' => 'Ответ успешно изменен'];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return $this->renderAjax('update', [
            'formModel' => $answerForm,
            'answerImageModel' => $answerImageForm,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id, Response $response): array
    {
        $this->response->format = Response::FORMAT_JSON;
        if (($answer = StoryTestAnswer::findOne($id)) === null) {
            throw new NotFoundHttpException('Ответ не найден');
        }
        $folder = Yii::getAlias('@public') . '/test_images';
        try {
            $this->answerService->delete($folder, $answer->id);
            return ['success' => true];
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionDeleteImage(int $id, Response $response)
    {
        $response->format = Response::FORMAT_JSON;
        if (($answer = StoryTestAnswer::findOne($id)) === null) {
            throw new NotFoundHttpException('Ответ не найден');
        }
        $folder = Yii::getAlias('@public') . '/test_images';
        try {
            $this->answerService->deleteImage($answer->id, $folder);
            return ['success' => true, 'message' => 'Изображение успешно удалено'];
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
}
