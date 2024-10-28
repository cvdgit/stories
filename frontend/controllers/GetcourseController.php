<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\User;
use common\services\UserService;
use Exception;
use frontend\GetCourse\SignupCommand;
use frontend\GetCourse\SignupHandler;
use frontend\GetCourse\WebhookForm;
use frontend\models\auth\CreateUserForm;
use Yii;
use yii\web\Controller;
use yii\web\Request;

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
                            $form->last_name
                        )
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
}
