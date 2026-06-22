<?php

declare(strict_types=1);

namespace backend\LlmPrompt;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;

class LlmPromptListFilterModel extends Model
{
    public $name;
    public $key;

    public function rules(): array
    {
        return [
            [['name', 'key'], 'string', 'max' => 50],
        ];
    }

    public function search(array $values): DataProviderInterface
    {
        $query = LlmPrompt::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
            'pagination' => false,
        ]);

        $this->load($values);
        if (!$this->validate()) {
            $query->andWhere('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'key', $this->key]);

        return $dataProvider;
    }
}
