<?php

declare(strict_types=1);

namespace backend\modules\repetition\Students;

use yii\base\Action;

class ListAction extends Action
{
    public function run(): string
    {
        $searchModel = new StudentListSearch();
        $dataProvider = $searchModel->search();
        return $this->controller->render('list', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
