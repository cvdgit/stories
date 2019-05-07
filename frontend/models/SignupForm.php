<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Пользователь с таким именем уже существует.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Пользователь с таким email уже существует.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'email' => 'Email',
            'password' => 'Пароль',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            throw new \DomainException('Signup model is not valid');
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->status = User::STATUS_WAIT;
        $user->group = User::GROUP_AUTHOR;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailConfirmToken();

        if (!$user->save(false)) {
            throw new \DomainException('Signup user save error');
        }

        $auth = Yii::$app->authManager;
        $authorRole = $auth->getRole('user');
        $auth->assign($authorRole, $user->getId());

        return $user;
    }

    public function sentEmailConfirm(User $user)
    {
        $sent = Yii::$app->mailer
            ->compose(['html' => 'userSignupComfirm-html', 'text' => 'userSignupComfirm-text'], ['user' => $user])
            ->setTo($user->email)
            ->setFrom([Yii::$app->params['infoEmail'] => Yii::$app->name])
            ->setSubject('Подтверждение регистрации')
            ->send();
        if (!$sent) {
            throw new \RuntimeException('Confirm email sent error');
        }
    }

    public function confirmation($token)
    {
        if (empty($token)) {
            throw new \DomainException('Empty confirm token.');
        }

        $user = User::findOne(['email_confirm_token' => $token]);
        if (!$user) {
            throw new \DomainException('User is not found.');
        }

        $user->email_confirm_token = null;
        $user->status = User::STATUS_ACTIVE;
        if (!$user->save()) {
            throw new \RuntimeException('Saving error.');
        }

        if (!Yii::$app->getUser()->login($user)) {
            throw new \RuntimeException('Error authentication.');
        }
    }

}
