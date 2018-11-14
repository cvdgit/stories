<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "payment".
 *
 * @property int $id
 * @property string $payment
 * @property string $finish
 * @property string $state
 * @property int $user_id
 * @property int $rate_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Rate $rate
 * @property User $user
 */
class Payment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%payment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment', 'finish', 'user_id', 'rate_id'], 'required'],
            [['created_at', 'updated_at', 'user_id', 'rate_id'], 'integer'],
            [['state'], 'string', 'max' => 255],
            [['payment', 'finish'], 'date', 'format' => 'yyyy-M-d H:m:s'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ИД',
            'payment' => 'Дата начала подписки',
            'finish' => 'Дата окончания подписки',
            'state' => 'Состояние',
            'user_id' => 'Пользователь',
            'rate_id' => 'Подписка',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRate()
    {
        return $this->hasOne(Rate::className(), ['id' => 'rate_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
