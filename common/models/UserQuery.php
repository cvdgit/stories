<?php

namespace common\models;

use yii\db\ActiveQuery;

class UserQuery extends ActiveQuery
{

    public function activeUsers()
    {
        return $this
            ->andWhere(['not', ['last_activity' => null]])
            ->andWhere(['status' => User::STATUS_ACTIVE]);
    }

}