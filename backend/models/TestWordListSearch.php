<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TestWordList;
use yii\data\DataProviderInterface;

/**
 * TestWordListSearch represents the model behind the search form of `common\models\TestWordList`.
 */
class TestWordListSearch extends Model
{
    public $id;
    public $created_at;
    public $name;

    public function rules(): array
    {
        return [
            ['created_at', 'date', 'format' => 'php:Y-m-d'],
            [['name'], 'string'],
        ];
    }

    public function search(array $params = []): DataProviderInterface
    {
        $query = TestWordList::find();

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

        $query->andFilterWhere(["DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d')" => $this->created_at]);;
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
