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

        $todayStories = $this->getStories(date('Y-m-d'));

        return $this->render('index', [
            'labels' => $labels,
            'data' => $data,
            'todayStories' => $todayStories,
        ]);
    }

    private function getStories(string $date): array
    {

        $betweenBegin = new Expression("UNIX_TIMESTAMP('$date 00:00:00')");
        $betweenEnd = new Expression("UNIX_TIMESTAMP('$date 23:59:59')");

        $query = (new Query())
            ->select([
                'story_id' => 't.story_id',
                'viewed_at' => 'MAX(t.created_at)'
            ])
            ->from(['t' => '{{%story_statistics}}'])
            ->where(['between', 't.created_at', $betweenBegin, $betweenEnd])
            ->groupBy(['t.story_id']);

        $query2 = (new Query())
            ->select([
                'story_id' => 't.story_id',
                'viewed_at' => 'MAX(t.created_at)'
            ])
            ->from(['t' => '{{%story_readonly_statistics}}'])
            ->where(['between', 'created_at', $betweenBegin, $betweenEnd])
            ->groupBy(['t.story_id']);

        $query->union($query2);

        return (new Query())
            ->select([
                'story_title' => 't2.title',
                'viewed_at' => 'MAX(t.viewed_at)',
            ])
            ->from(['t' => $query])
            ->innerJoin(['t2' => 'story'], 't.story_id = t2.id')
            ->groupBy(['t.story_id'])
            ->orderBy(['MAX(t.viewed_at)' => SORT_DESC])
            ->all();
    }
}
