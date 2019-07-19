<?php
namespace backend\controllers;

use yii\filters\AccessControl;
use common\models\StoryStatisticsSearch;
use common\rbac\UserRoles;
use yii\web\Controller;
use yii\web\ErrorAction;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['error'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_ADMIN_PANEL],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function actionIndex()
    {
        $statisticsModel = new StoryStatisticsSearch();
        $dataProvider = $statisticsModel->getChartData4();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'storyViewsData' => $statisticsModel->chartStoryViews(),
        ]);
    }

}
