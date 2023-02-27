<?php

declare(strict_types=1);

namespace backend\modules\repetition\Repetition\TestingList;

use common\models\StoryTest;
use yii\base\Action;
use yii\web\NotFoundHttpException;

class ListAction extends Action
{
    /**
     * @throws NotFoundHttpException
     */
    public function run(int $test_id): string
    {
        $testing = StoryTest::findOne($test_id);
        if ($testing === null) {
            throw new NotFoundHttpException('Тест не найден');
        }

        $searchModel = new RepetitionListSearch();
        $dataProvider = $searchModel->search($testing->id);

        return $this->controller->renderAjax('list', [
            'dataProvider' => $dataProvider,
            'testId' => $testing->id,
        ]);
    }
}
