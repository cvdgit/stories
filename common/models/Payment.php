<?php

namespace common\models;

use DomainException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
 * @property string $data
 *
 * @property Rate $rate
 * @property User $user
 */
class Payment extends ActiveRecord
{

    const STATUS_NEW = 0;
    const STATUS_VALID = 1;
    const STATUS_INVALID = 2;

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
            TimestampBehavior::class,
        ];
    }

    public static function find()
    {
        return new PaymentQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment', 'finish', 'user_id', 'rate_id'], 'required'],
            [['created_at', 'updated_at', 'user_id', 'rate_id'], 'integer'],
            [['state', 'data'], 'safe'],
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
     * @return ActiveQuery
     */
    public function getRate()
    {
        return $this->hasOne(Rate::class, ['id' => 'rate_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function create($userID, $rateID, $dateStart, $dateFinish, $state = self::STATUS_NEW)
    {
        $payment = new static();
        $payment->user_id = $userID;
        $payment->rate_id = $rateID;
        $payment->payment = $dateStart;
        $payment->finish = $dateFinish;
        $payment->state = $state;
        return $payment;
    }

    public static function getUserPaymentHistory($userID)
    {
        return self::find()->paymentsByUser($userID);
    }

    public static function findModel($id): self
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new DomainException('Платеж не найден.');
    }

    public function isNew(): bool
    {
        return (int)$this->state === self::STATUS_NEW;
    }

    public function isValid(): bool
    {
        return (int)$this->state === self::STATUS_VALID;
    }

}
