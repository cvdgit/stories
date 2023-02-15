<?php

declare(strict_types=1);

namespace backend\modules\repetition\Schedule;

use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\data\SqlDataProvider;
use yii\db\Query;

class ScheduleSearch extends Model
{
    public function search(): DataProviderInterface
    {
        $query = (new Query())
            ->select('id,name')
            ->from('schedule');

        return new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),
            'totalCount' => $query->count(),
            'key' => 'id',
            'pagination' => false,
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC],
                'attributes' => [
                    'name',
                ],
            ]
        ]);
    }
}
