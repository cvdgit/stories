<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Rate model
 *
 * @property integer $id
 * @property datetime  $payment
 * @property datetime  $finish
 * @property string $state
 * @property integer $user_id
 * @property integer  $rate_id
 * @property integer $created_at
 * @property integer  $updated_at
 */
class Payment extends ActiveRecord
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

}
