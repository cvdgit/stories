<?php

namespace modules\files\forms;

use modules\files\models\StudyFile;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * StudyFileSearch represents the model behind the search form of `common\models\StudyFile`.
 */
class StudyFileSearch extends Model
{

    public $name;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'string', 'max' => 50],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = StudyFile::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
