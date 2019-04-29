<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "rate".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $cost
 * @property string $type
 * @property integer $days
 * @property string $code
 * 
 * @property Payment[] $payments
 */
class Rate extends ActiveRecord
{

    const ACTIVE = 'active';
    const ARCHIVE = 'archive';

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
            [['cost', 'days', 'code'], 'required'],
            [['cost', 'days'], 'integer'],
            ['code', 'string', 'max' => 50],
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
            'days' => 'Количество дней',
            'type' => 'Тип подписки',
            'code' => 'Код',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payment::class, ['rate_id' => 'id']);
    }

    public static function findRateByCode($code)
    {
        return self::findOne(['code' => $code]);
    }

    public function isFreeSubscription(): bool
    {
        return ($this->code === 'free');
    }

    public function isArchive(): bool
    {
        return $this->type === self::ARCHIVE;
    }

}