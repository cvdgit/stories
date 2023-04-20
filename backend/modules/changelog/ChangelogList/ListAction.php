<?php

declare(strict_types=1);

namespace backend\modules\changelog\ChangelogList;

use backend\modules\changelog\models\Changelog;
use yii\base\Action;
use yii\data\ActiveDataProvider;

class ListAction extends Action
{
    public function run(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Changelog::find(),
        ]);

        return $this->controller->render('list', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
