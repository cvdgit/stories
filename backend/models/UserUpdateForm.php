<?php


namespace backend\models;


use common\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class UserUpdateForm extends Model
{

    public $id;
    public $username;
    public $email;
    public $role;
    public $status;

    private $_user;

    public function __construct(User $user, $config = [])
    {
        $this->id = $user->id;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->status = $user->status;
        $roles = Yii::$app->authManager->getRolesByUser($user->id);
        $this->role = $roles ? reset($roles)->name : null;
        $this->_user = $user;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['username', 'email', 'role', 'status'], 'required'],
            ['email', 'email'],
            [['username', 'email'], 'string', 'max' => 255],
            [['username', 'email'], 'unique', 'targetClass' => User::class, 'filter' => ['<>', 'id', $this->_user->id]],
            ['status', 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Имя пользователя',
            'email' => 'Email',
            'password' => 'Пароль',
            'status' => 'Статус',
            'role' => 'Роль',
        ];
    }

    public function rolesList(): array
    {
        return ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description');
    }

}