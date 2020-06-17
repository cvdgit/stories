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
        $query->select(['question_topic_name', 'entity_name', 'relation_name', 'SUM(correct_answer) AS correct_answers', 'MAX(created_at) AS max_created_at']);
        $query->groupBy(['question_topic_name', 'entity_name', 'relation_name']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => ['max_created_at'],
                'defaultOrder' => ['max_created_at' => SORT_DESC],
            ],
            'pagination' => false,
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