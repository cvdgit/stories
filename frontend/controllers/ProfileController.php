<?php

namespace frontend\controllers;

use common\services\ProfileService;
use Exception;
use frontend\components\UserController;
use frontend\models\ProfileEditForm;
use Yii;
use common\models\User;

class ProfileController extends UserController
{

    protected $profileService;

    public function __construct($id, $module, ProfileService $profileService, $config = [])
    {
        $this->profileService = $profileService;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $user = User::findModel(Yii::$app->user->id);
        return $this->render('index', [
            'model' => $user,
            'activePayment' => $user->getActivePayment()->one(),
        ]);
    }

    public function actionUpdate()
    {
        $user = User::findModel(Yii::$app->user->id);
        $form = new ProfileEditForm($user->profile);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $form->photoForm->load(Yii::$app->request->post());
            $form->photoForm->validate();
            try {
                $this->profileService->update($user, $form);
                Yii::$app->session->setFlash('success', 'Профиль успешно обновлен');
                return $this->redirect(['/profile']);
            }
            catch (Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', 'При редактировании профиля возникла ошибка');
            }
        }
        return $this->render('update', [
            'model' => $form,
        ]);
    }

/*    public function actionChangePassword()
    {
        try {
            $model = new ChangePasswordForm();
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->changePassword()) {
            Yii::$app->session->setFlash('password-message', 'Новый пароль сохранен!');
        }
        else {
            Yii::$app->session->setFlash('password-message', 'Ошибка! Пароль не изменен!');
        }
        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }*/

}