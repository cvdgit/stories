<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\components\StoryBreadcrumbsBuilder;
use backend\components\StorySideBarMenuItemsBuilder;
use yii\filters\AccessControl;
use common\models\Story;
use common\models\StoryStatisticsSearch;
use common\rbac\UserRoles;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class StatisticsController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_STATISTICS_ACCESS],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionList($id): string
    {
		$model = Story::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException("История не найдена");
        }
		$searchModel = new StoryStatisticsSearch();
        $chartData = $searchModel->getChartData($id);
        $chartData2 = $searchModel->getChartData2($id);
        $chartData3 = $searchModel->getChartData3($id);
		return $this->render('list', [
			'model' => $model,
            'chartData' => $chartData,
            'chartData2' => $chartData2,
            'chartData3' => $chartData3,
            "sidebarMenuItems" => (new StorySideBarMenuItemsBuilder($model))->build(),
            "breadcrumbs" => (new StoryBreadcrumbsBuilder($model, 'Статистика: ' . $model->title))->build(),
            "title" => 'Статистика: ' . $model->title,
		]);
	}
}
