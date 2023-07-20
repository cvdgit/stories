<?php

declare(strict_types=1);

namespace backend\Testing\ImportQuestions\SelectTest;

use yii\base\Action;
use yii\db\Query;
use yii\web\Response;

class SelectTestAction extends Action
{
    public function run(int $to_test_id, string $query, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        return (new Query())
            ->select([
                'title',
                'id',
            ])
            ->from('story_test')
            ->where(['like', 'title', $query])
            //->andWhere(['not in', 'id', [$to_test_id]])
            ->orderBy(['title' => SORT_ASC])
            ->all();
    }
}
