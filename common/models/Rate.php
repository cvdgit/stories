<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "rate".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $cost
 * @property int $mounth_count
 * @property string $type
 *
 * @property Payment[] $payments
 */
class Rate extends \yii\db\ActiveRecord
{


    const ACTIVE = 'active';
    const ARCHIVE = 'archive';
    private $dataPayment;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rate}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cost', 'mounth_count'], 'required'],
            [['cost', 'mounth_count'], 'integer'],
            ['type', 'in', 'range' => [self::ACTIVE, self::ARCHIVE]],
            ['type', 'default', 'value' => self::ACTIVE],
            [['description', 'title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'description' => 'Описание',
            'cost' => 'Стоимость',
            'mounth_count' => 'Количество месяцев',
            'type' => 'Тип подписки',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payment::className(), ['rate_id' => 'id']);
    }

    public function getDataPayment()
    {
        return $this->dataPayment;
    }

    public function setDataPayment($dataPayment)
    {
        $this->dataPayment = $dataPayment;
    }

}
