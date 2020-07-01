<?php

namespace backend\models;

use common\models\UserQuestionHistory;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserQuestionHistorySearch extends Model
{

    public $student_id;

    public function __construct(int $studentID, $config = [])
    {
        $this->student_id = $studentID;
        parent::__construct($config);
    }

    public function search($params): ActiveDataProvider
    {
        $query = UserQuestionHistory::find()->innerJoinWith('userQuestionAnswers');
        //$query->select(['question_topic_name', 'entity_name', 'relation_name', 'correct_answer', 'created_at', "`123` AS answers"]);
        //$query->groupBy(['question_topic_name', 'entity_name', 'relation_name']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => ['created_at'],
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
            'pagination' => false,
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'student_id' => $this->student_id,
        ]);

        return $dataProvider;
    }

}