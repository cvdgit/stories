<?php


namespace api\modules\v1\models;


use yii\data\ActiveDataProvider;

class StorySearch extends \yii\base\Model
{

    public $title;

    public function rules()
    {
        return [
            ['title', 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Story::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($params, '');
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }

}