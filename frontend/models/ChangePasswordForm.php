<?php
namespace frontend\models;

use yii\base\Model;
use yii\base\InvalidParamException;
use common\models\User;
use Yii;

/**
 * Password change form
 */
class ChangePasswordForm extends Model
{
    public $password;
    public $passwordRepeat;

    /**
     * @var \common\models\User
     */
    private $_user;


    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($config = [])
    {
        $this->_user = User::findModel(Yii::$app->user->id);
        if (!$this->_user) {
            throw new InvalidParamException('Пользователь не найден.');
        }
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['passwordRepeat', 'required'],
            ['passwordRepeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Пароли не совпадают" ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'password' => 'Пароль',
            'passwordRepeat' => 'Повторить пароль'
        ];
    }

    /**
     * Changes password.
     *
     * @return bool if password was change.
     */
    public function changePassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        return $user->save(false);
    }
}
