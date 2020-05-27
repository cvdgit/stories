<?php


namespace api\modules\v1\models;

use yii\db\ActiveRecord;

class Story extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%story}}';
    }

    public function fields()
    {
        return [
            'id',
            'title',
        ];
    }

    public function extraFields()
    {
        return [];
    }

}