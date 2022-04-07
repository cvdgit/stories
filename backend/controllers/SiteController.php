<?php
namespace backend\controllers;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
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
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function actionIndex(): string
    {

        $week = date('W');
        $year = date('Y');
        $date = new \DateTime();
        $date->setISODate($year, $week);
        $weekStartDate = clone $date;
        $weekEndDate = clone $date->modify('+6 days');

        $targetDate = clone $weekStartDate;
        $labels = [];
        $data = [];
        $i = 0;
        while ($targetDate <= $weekEndDate) {

            $labels[$i] = Yii::$app->formatter
                ->asDate($targetDate->format('d.m.Y'), 'php:d F');

            $formatDate = $targetDate->format('Y-m-d');
            $betweenBegin = new Expression("UNIX_TIMESTAMP('$formatDate 00:00:00')");
            $betweenEnd = new Expression("UNIX_TIMESTAMP('$formatDate 23:59:59')");

            $query = (new Query())
                ->select(['views' => 'COUNT(DISTINCT `session`)'])
                ->from('{{%story_statistics}}')
                ->where(['between', 'created_at', $betweenBegin, $betweenEnd]);

            $query2 = (new Query())
                ->select(['views' => 'COUNT(DISTINCT `story_id`)'])
                ->from('{{%story_readonly_statistics}}')
                ->where(['between', 'created_at', $betweenBegin, $betweenEnd]);

            $query->union($query2, true);

            $data[$i] = (new Query())
                ->select('SUM(a.views)')
                ->from(['a' => $query])
                ->scalar();

            $targetDate = $targetDate->modify('+1 day');
            $i++;
        }

        return $this->render('index', [
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}
