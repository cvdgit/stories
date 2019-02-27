<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use common\models\StoryFeedback;

/**
 * StoryFeedbackSearch represents the model behind the search form of `common\models\StoryFeedback`.
 */
class StoryFeedbackSearch extends StoryFeedback
{

    public function rules()
    {
        return [
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = StoryFeedback::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        
        $sort = new Sort([
            'defaultOrder' => ['created_at' => SORT_DESC],
        ]);
        $dataProvider->setSort($sort);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }

}
