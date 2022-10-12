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
                'fio' => "COALESCE(CONCAT(profile.last_name, ' ', profile.first_name), user.username)",
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
                        'asc' => ["COALESCE(CONCAT(profile.last_name, ' ', profile.first_name), user.username)" => SORT_ASC],
                        'desc' => ["COALESCE(CONCAT(profile.last_name, ' ', profile.first_name), user.username)" => SORT_DESC],
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

        $query->andFilterWhere(['like', "COALESCE(CONCAT(profile.last_name, ' ', profile.first_name), user.username)", $this->fio]);
        $query->andFilterWhere(['like', 'email', $this->email]);
        $query->andFilterWhere([
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
