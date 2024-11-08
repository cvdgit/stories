<?php

declare(strict_types=1);

namespace modules\edu\controllers\admin;

use modules\edu\models\EduStory;
use Yii;
use yii\data\SqlDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Controller;

class StoryController extends Controller
{
    public function actionIndex(): string
    {
        $pathQuery = (new Query())
            ->select(
                new Expression("CONCAT(c.name, ' / ', p.name, ' / ', t.name, ' / ', l.name)"),
            )
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
                'publishedAt' => 's.published_at',
                'path' => $pathQuery,
            ])
            ->from(['s' => 'story'])
            ->where('s.published_at IS NOT NULL')
            ->andWhere(['between', 's.published_at', $betweenBegin, $betweenEnd])
            ->orderBy(['s.published_at' => SORT_DESC]);

        $dataProvider = new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),
            'totalCount' => $query->count(),
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
