<?php

namespace modules\files\forms;

use modules\files\models\StudyFolder;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * StudyFolderSearch represents the model behind the search form of `common\models\StudyFolder`.
 */
class StudyFolderSearch extends Model
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
        $query = StudyFolder::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC],
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
