<?php

namespace api\modules\v1\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class StorySearch extends Model
{

    public $title;
    public $category_id;

    public function rules()
    {
        return [
            ['title', 'safe'],
            ['category_id', 'integer'],
        ];
    }

    public function search($params)
    {
        $query = Story::find();
        $query->joinWith(['categories']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($params, '');
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'title', $this->title]);

        if (!empty($this->category_id)) {
            $query->andFilterWhere(['in', 'category.id', explode(',', $this->category_id)]);
        }

        return $dataProvider;
    }

}