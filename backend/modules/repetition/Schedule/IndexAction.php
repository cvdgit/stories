<?php

declare(strict_types=1);

namespace backend\modules\repetition\Schedule;

use yii\base\Action;

class IndexAction extends Action
{
    public function run(): string
    {
        $searchModel = new ScheduleSearch();
        $dataProvider = $searchModel->search();
        return $this->controller->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
