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

        $date = new \DateTime('now');
        $endDate = clone $date;
        $startDate = clone $date->modify('-6 days');

        $targetDate = clone $startDate;
        $labels = [];
        $data = [];
        $i = 0;
        while ($targetDate <= $endDate) {

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
            'users' => $this->getUsers(date('Y-m-d')),
        ]);
    }

    private function getUsers(string $date): array
    {
        $betweenBegin = new Expression("UNIX_TIMESTAMP('$date 00:00:00')");
        $betweenEnd = new Expression("UNIX_TIMESTAMP('$date 23:59:59')");
        $query = (new Query())->select([
                'user_name' => "IFNULL(CONCAT(t2.last_name, ' ', t2.first_name), t.email)",
                'user_active_at' => 't.last_activity',
            ])
            ->from(['t' => 'user'])
            ->leftJoin(['t2' => 'profile'], 't2.user_id = t.id')
            ->where(['between', 't.last_activity', $betweenBegin, $betweenEnd])
            ->orderBy(['t.last_activity' => SORT_DESC]);
        return $query->all();
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
