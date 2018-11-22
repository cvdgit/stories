<?php

namespace common\models;

use Yii;

class StoryQuery extends \yii\db\ActiveQuery
{
    public function published()
    {
        return $this->andWhere(['{{%story}}.status' => Story::STATUS_PUBLISHED]);
    }

    public function bySubAccess()
    {
    	return $this->orderBy(['{{%story}}.sub_access' => SORT_DESC]);
    }

    public function lastStories()
    {
    	return $this->orderBy(['{{%story}}.created_at' => SORT_DESC])->limit(8);
    }
}
