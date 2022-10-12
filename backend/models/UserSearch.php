<?php

declare(strict_types=1);

namespace backend\models;

use common\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;

class UserSearch extends Model
{
    public $fio;
    public $email;
    public $status;

    public function rules(): array
    {
        return [
            [['fio', 'email'], 'string'],
            ['status', 'integer'],
        ];
    }

    public function search(array $params): DataProviderInterface
    {
        $query = User::find()
            ->select([
                'user.*',
                'fio' => "CONCAT(profile.last_name, ' ', profile.first_name)",
                'source' => 'auth.source',
            ])
            ->leftJoin('auth', 'auth.user_id = user.id')
            ->leftJoin('profile', 'profile.user_id = user.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'last_activity' => SORT_DESC,
                ],
                'attributes' => [
                    'id',
                    'fio' => [
                        'asc' => ["CONCAT(profile.last_name, ' ', profile.first_name)" => SORT_ASC],
                        'desc' => ["CONCAT(profile.last_name, ' ', profile.first_name)" => SORT_DESC],
                    ],
                    'email',
                    'last_activity',
                    'created_at',
                ],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'or',
            ['like', 'profile.first_name', $this->fio],
            ['like', 'profile.last_name', $this->fio],
        ]);
        $query->andFilterWhere(['like', 'email', $this->email]);
        $query->andFilterWhere([
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
