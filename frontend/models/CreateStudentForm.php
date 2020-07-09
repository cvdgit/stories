<?php

namespace frontend\models;

use common\models\UserStudent;
use yii\base\Model;

class CreateStudentForm extends Model
{

    public $name;
    public $birth_date;

    protected $user_id;

    public function __construct($userID, $config = [])
    {
        $this->user_id = $userID;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['name', 'birth_date'], 'required'],
            ['name', 'string', 'max' => 50],
            ['birth_date', 'default', 'value' => null],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'birth_date' => 'Дата рождения',
        ];
    }

    public function createStudent()
    {
        if (!$this->validate()) {
            throw new \RuntimeException('Model not valid');
        }
        $model = UserStudent::createStudent($this->user_id, $this->name, $this->birth_date);
        $model->save();
    }

}