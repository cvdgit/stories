<?php

namespace common\models;

use Yii;
use yii\db\Expression;

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

    public function withCover()
    {
        return $this->andWhere(['not', ['{{%story}}.cover' => null]]);
    }

    public function byRand()
    {
        return $this->orderBy(new Expression('rand()'));
    }

}
