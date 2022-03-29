<?php

namespace frontend\models;

use common\models\ContactRequest;
use yii\base\Model;

class ContactRequestForm extends Model
{

    public $name;
    public $phone;
    public $email;
    public $text;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name', 'phone', 'email', 'text'], 'required'],
            [['name', 'phone', 'email'], 'string', 'max' => 50],
            ['email', 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'ФИО',
            'phone' => 'Номер телефона',
            'text' => 'Вопрос',
            'email' => 'Email',
        ];
    }

    public function create(): void
    {
        if (!$this->validate()) {
            throw new \DomainException('ContactRequestForm not valid');
        }
        $model = ContactRequest::create($this->name, $this->phone, $this->email, $this->text);
        if (!$model->save()) {
            throw new \DomainException('ContactRequestForm save exception');
        }
    }
}