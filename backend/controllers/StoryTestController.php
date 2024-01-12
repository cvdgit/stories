<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\components\story\HTMLBLock;
use backend\components\story\reader\HTMLReader;
use backend\components\story\TestBlock;
use backend\components\StorySideBarMenuItemsBuilder;
use backend\Story\Tests\StoryTests\StoryTestsAction;
use backend\Story\Tests\UpdatePassTestsRepeat\UpdatePassTestsRepeatAction;
use backend\Story\Tests\UpdatePassTestsRepeat\UpdatePassTestsRepeatFormAction;
use backend\Story\Tests\UpdateTestsRepeat\UpdateTestsRepeatAction;
use backend\Story\Tests\UpdateTestsRepeat\UpdateTestsRepeatFormAction;
use common\models\Story;
use common\models\StoryTest;
use common\rbac\UserRoles;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class StoryTestController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_STORIES],
                    ],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            "index" => StoryTestsAction::class,
            "update-repeat-form" => UpdateTestsRepeatFormAction::class,
            "update-repeat" => UpdateTestsRepeatAction::class,
            "update-pass-test-repeat-form" => UpdatePassTestsRepeatFormAction::class,
            "update-pass-test-repeat" => UpdatePassTestsRepeatAction::class,
        ];
    }
}
