<?php

declare(strict_types=1);

namespace modules\edu\forms\admin;

use modules\edu\models\EduStudent;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;

class StudentSearch extends Model
{
    public $name;

    public function rules(): array
    {
        return [
            ['name', 'string'],
        ];
    }

    public function search(array $params): DataProviderInterface
    {
        $query = EduStudent::find();
        $query->leftJoin('user', 'user_student.user_id = user.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['user.last_activity' => SORT_DESC],
                'attributes' => [
                    'name',
                    'user.last_activity',
                ],
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->andWhere('1 = 0');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'user_student.name', $this->name]);

        return $dataProvider;
    }
}
