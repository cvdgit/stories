<?php

namespace backend\models\search;

use common\models\StoryTest;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class TestSearch extends Model
{

    public $title;
    public $created_at;
    public $source;
    public $answer_type;

    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 50],
            [['created_at'], 'date'],
            [['source', 'answer_type'], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = StoryTest::find()->with('storyTestQuestions');
        $query->where('parent_id = 0');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere([
            "DATE_FORMAT(FROM_UNIXTIME(created_at), '%d.%m.%Y')" => $this->created_at,
        ]);
        $query->andFilterWhere(['source' => $this->source]);
        $query->andFilterWhere(['answer_type' => $this->answer_type]);

        return $dataProvider;
    }

}