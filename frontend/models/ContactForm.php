<?php

namespace frontend\models;

use common\helpers\EmailHelper;
use RuntimeException;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $verifyCode;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'subject' => 'Тема',
            'body' => 'Сообщение',
            'verifyCode' => 'Код подтверждения',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param string $email the target email address
     */
    public function sendEmail($email): void
    {
        $response = EmailHelper::sendEmail($email, 'Сообщение с формы контакты от ' . $this->email, 'contact', ['form' => $this]);
        if (!$response->isSuccess()) {
            Yii::error($response->getApiResponse(), 'email.contact');
            throw new RuntimeException('Email not sent (contact)');
        }
    }
}
