<?php

namespace modules\edu\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\edu\models\EduClassProgram;

/**
 * EduClassProgramSearch represents the model behind the search form of `modules\edu\models\EduClassProgram`.
 */
class EduClassProgramSearch extends EduClassProgram
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'class_id', 'program_id'], 'integer'],
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
        $query = EduClassProgram::find();

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
            'class_id' => $this->class_id,
            'program_id' => $this->program_id,
        ]);

        return $dataProvider;
    }
}
