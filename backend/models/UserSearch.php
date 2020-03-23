<?php


namespace backend\models;


use common\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends Model
{

    public $username;
    public $email;
    public $status;

    public function rules()
    {
        return [
            [['username', 'email'], 'string'],
            ['status', 'integer'],
        ];
    }

    public function search($params)
    {
        $query = User::find();
        $query->with(['auth']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'last_activity' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'username', $this->username]);
        $query->andFilterWhere(['like', 'email', $this->email]);
        $query->andFilterWhere([
            'status' => $this->status,
        ]);

        return $dataProvider;
    }

}