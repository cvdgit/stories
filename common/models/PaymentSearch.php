<?php

namespace common\models;

use yii\data\ActiveDataProvider;

class PaymentSearch extends Payment
{

    public function search($params, $userID)
    {
        $query = static::getUserPaymentHistory($userID);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }

}
