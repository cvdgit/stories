<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\Expression;

class StoryQuery extends ActiveQuery
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

    public function byCategories($ids)
    {
        return $this->innerJoinWith('categories')->andWhere(['in', 'category.id', $ids])->limit(8);
    }

    public function audio()
    {
        return $this->andWhere(['{{%story}}.audio' => 1]);
    }

}
