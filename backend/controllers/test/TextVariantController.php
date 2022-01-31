<?php

namespace backend\controllers\test;

use backend\components\BaseController;
use backend\models\question\text\CreateTextVariantForm;
use common\models\StoryTest;
use common\rbac\UserRoles;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class TextVariantController extends BaseController
{

    /**
     * @inheritdoc
     */
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
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionCreate(int $test_id): string
    {
        /** @var StoryTest $testModel */
        $testModel = $this->findModel(StoryTest::class, $test_id);

        $form = new CreateTextVariantForm();
        if ($form->load($this->request->post())) {

        }

        return $this->render('create', [
            'testModel' => $testModel,
            'model' => $form,
        ]);
    }
}