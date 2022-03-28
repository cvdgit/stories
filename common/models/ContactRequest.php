<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "contact_request".
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $text
 * @property int $created_at
 * @property int $updated_at
 */
class ContactRequest extends ActiveRecord
{

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'contact_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'phone', 'text'], 'required'],
            [['text'], 'string'],
            [['name', 'phone'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'phone' => 'Phone',
            'text' => 'Text',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function create(string $name, string $phone, string $text): self
    {
        $model = new self();
        $model->name = $name;
        $model->phone = $phone;
        $model->text = $text;
        return $model;
    }
}