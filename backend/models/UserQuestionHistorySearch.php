<?php


namespace backend\models;


use common\models\UserQuestionHistory;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserQuestionHistorySearch extends Model
{

    public $user_id;

    public function __construct(int $userID, $config = [])
    {
        $this->user_id = $userID;
        parent::__construct($config);
    }

    public function search($params): ActiveDataProvider
    {
        $query = UserQuestionHistory::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'user_id' => $this->user_id,
        ]);

        return $dataProvider;
    }

}