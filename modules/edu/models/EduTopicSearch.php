<?php

namespace modules\edu\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\edu\models\EduTopic;

/**
 * EduTopicSearch represents the model behind the search form of `modules\edu\models\EduTopic`.
 */
class EduTopicSearch extends EduTopic
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'class_program_id', 'order'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
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
        $query = EduTopic::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'class_program_id' => $this->class_program_id,
            'order' => $this->order,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
