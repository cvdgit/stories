<?php

namespace frontend\models;

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
     * @return bool whether the email was sent
     */
    public function sendEmail($email)
    {
        if (!Yii::$app->mailer
            ->compose()
            ->setTo($email)
            ->setFrom(Yii::$app->params['infoEmail'])
            ->setSubject('Сообщение с формы контакты от ' . $this->email)
            ->setTextBody($this->email . "\n" . $this->name . "\n" . $this->subject . "\n" . $this->body)
            ->send()) {
            throw new \RuntimeException('Email not sent (contact)');
        }
    }
}
