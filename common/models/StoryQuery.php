<?php

namespace common\models;

use Yii;

class StoryQuery extends \yii\db\ActiveQuery
{
    public function published()
    {
        return $this->andWhere(['status' => Story::STATUS_PUBLISHED]);
    }
}
