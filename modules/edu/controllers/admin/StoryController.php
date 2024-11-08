<?php

declare(strict_types=1);

namespace modules\edu\controllers\admin;

use yii\data\SqlDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Controller;

class StoryController extends Controller
{
    public function actionIndex(): string
    {
        $pathQuery = (new Query())
            ->from(['sl' => 'edu_lesson_story'])
            ->innerJoin(['l' => 'edu_lesson'], 'sl.lesson_id = l.id')
            ->innerJoin(['t' => 'edu_topic'], 'l.topic_id = t.id')
            ->innerJoin(['cp' => 'edu_class_program'], 't.class_program_id = cp.id')
            ->innerJoin(['p' => 'edu_program'], 'cp.program_id = p.id')
            ->innerJoin(['c' => 'edu_class'], 'cp.class_id = c.id')
            ->where('s.id = sl.story_id');

        $fromDate = (new \DateTime())->modify('-1year')->format('Y-m-d');
        $betweenBegin = new Expression("UNIX_TIMESTAMP('$fromDate 00:00:00')");

        $toDate = (new \DateTime())->format('Y-m-d');
        $betweenEnd = new Expression("UNIX_TIMESTAMP('$toDate 23:59:59')");

        $query = (new Query())
            ->select([
                'id' => 's.id',
                'title' => 's.title',
                'alias' => 's.alias',
                'publishedAt' => 's.published_at',
                'path' => $pathQuery->select(
                    new Expression(
                        "GROUP_CONCAT(CONCAT(
                        CONCAT('<a target=\"_blank\" href=\"/admin/index.php?r=edu/admin/class/update&id=', c.id, '\">', c.name, '</a>'),
                        ' / ',
                        CONCAT('<a target=\"_blank\" href=\"/admin/index.php?r=edu/admin/program/update&id=', p.id, '\">', p.name, '</a>'),
                        ' / ',
                        CONCAT('<a target=\"_blank\" href=\"/admin/index.php?r=edu/admin/topic/update&id=', t.id, '\">', t.name, '</a>'),
                        ' / ',
                        CONCAT('<a target=\"_blank\" href=\"/admin/index.php?r=edu/admin/lesson/update&id=', l.id, '\">', l.name, '</a>')
                        ) SEPARATOR ', ')",
                    ),
                ),
                'author' => new Expression("COALESCE(CONCAT(p.last_name, ' ', p.first_name), u.username)"),
            ])
            ->from(['s' => 'story'])
            ->innerJoin(['u' => 'user'], 's.user_id = u.id')
            ->leftJoin(['p' => 'profile'], 'u.id = p.user_id')
            ->where('s.published_at IS NOT NULL')
            ->andWhere(['between', 's.published_at', $betweenBegin, $betweenEnd]);

        $dataProvider = new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),
            'totalCount' => $query->count(),
            'sort' => [
                'attributes' => [
                    'id',
                    'title',
                    'publishedAt',
                    'author',
                ],
                'defaultOrder' => ['publishedAt' => SORT_DESC],
            ],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
