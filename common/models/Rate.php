<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Rate model
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $cost
 * @property integer $mounth_count
 * @property string $type
 */
class Rate extends ActiveRecord
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

    public function getDataPayment()
    {
        return $this->dataPayment;
    }

    public function setDataPayment($dataPayment)
    {
        $this->dataPayment = $dataPayment;
    }

}
