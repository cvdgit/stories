<?php

declare(strict_types=1);

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
 * @property string $email
 * @property string $text
 * @property int $created_at
 * @property int $updated_at
 * @property string $comment
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
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'phone' => 'Номер телефона',
            'text' => 'Текст',
            'email' => 'Email',
            'created_at' => 'Дата создания',
            'comment' => 'Комментарий',
        ];
    }

    public static function create(string $name, string $phone, string $email, string $text): self
    {
        $model = new self();
        $model->name = $name;
        $model->phone = $phone;
        $model->email = $email;
        $model->text = $text;
        return $model;
    }
}
