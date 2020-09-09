<?php

namespace backend\models\search;

use common\models\StoryTest;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class TestSearch extends Model
{

    public $title;
    public $header;
    public $created_at;
    public $remote;
    public $question_number;

    public function rules()
    {
        return [
            [['title', 'header'], 'string', 'max' => 50],
            [['created_at'], 'date'],
            [['remote', 'question_number'], 'integer'],
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
        $query->andFilterWhere(['like', 'header', $this->header]);
        $query->andFilterWhere([
            "DATE_FORMAT(FROM_UNIXTIME(created_at), '%d.%m.%Y')" => $this->created_at,
        ]);
        $query->andFilterWhere(['remote' => $this->remote]);

        return $dataProvider;
    }

}