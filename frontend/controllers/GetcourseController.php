<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\User;
use common\services\UserService;
use Exception;
use frontend\GetCourse\SignupCommand;
use frontend\GetCourse\SignupHandler;
use frontend\GetCourse\WebhookForm;
use Yii;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class GetcourseController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @var SignupHandler
     */
    private $signupHandler;

    public function __construct($id, $module, UserService $userService, SignupHandler $signupHandler, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->signupHandler = $signupHandler;
    }

    public function actionWebhook(Request $request): void
    {
        $form = new WebhookForm();
        if ($form->load($request->get(), '') && $form->validate()) {
            $user = User::findOne(['get_course_id' => $form->id]);
            if ($user === null) {
                $user = User::findOne(['email' => $form->email]);
            }

            if ($user === null) {
                try {
                    $this->signupHandler->handle(
                        new SignupCommand(
                            (int) $form->id,
                            $form->email,
                            $form->first_name,
                            $form->last_name,
                        ),
                    );
                } catch (Exception $ex) {
                    Yii::$app->errorHandler->logException($ex);
                }
            } else {
                $user->updateGetCourseId((int) $form->id);
                if (!$user->save()) {
                }
            }
        }
    }

    /**
     * @throws BadRequestHttpException
     */
    public function actionMentalMap(string $id, Request $request): string
    {
        $this->layout = 'video';

        $getCourseUserId = $request->get('get_course_id');
        if (empty($getCourseUserId)) {
            throw new BadRequestHttpException('Нет обязательных параметров');
        }

        return $this->render('mental-map', [
            'getCourseUserId' => $getCourseUserId,
            'mentalMapId' => $id,
        ]);
    }

    public function actionAuth(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;
        $payload = Json::decode($request->rawBody);
        $getCourseUserId = $payload['id'] ?? null;

        if ($getCourseUserId === null) {
            return ['success' => false, 'message' => 'GetCourse user id not found'];
        }

        $userModel = User::findOne(['get_course_id' => (int) $getCourseUserId]);
        if ($userModel === null) {
            return ['success' => false, 'message' => 'GetCourse user not found'];
        }

        if (Yii::$app->user->isGuest) {
            try {
                Yii::$app->user->login($userModel, Yii::$app->params['user.rememberMeDuration']);
                return ['success' => true, 'student_id' => $userModel->getStudentID()];
            } catch (Exception $exception) {
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }

        if ($userModel->get_course_id !== (int) $getCourseUserId) {
            return ['success' => false, 'message' => 'Linked user error'];
        }

        return ['success' => true, 'student_id' => $userModel->getStudentID()];
    }

    /**
     * @throws BadRequestHttpException
     */
    public function actionQuiz(int $id, Request $request): string
    {
        $this->layout = 'video';

        $getCourseUserId = $request->get('get_course_id');
        if (empty($getCourseUserId)) {
            throw new BadRequestHttpException('Нет обязательных параметров');
        }

        return $this->render('quiz', [
            'getCourseUserId' => $getCourseUserId,
            'quizId' => $id,
        ]);
    }
}
