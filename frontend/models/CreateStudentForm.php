<?php

namespace frontend\models;

use common\models\UserStudent;
use http\Exception\RuntimeException;
use yii\base\Model;

class CreateStudentForm extends Model
{

    public $name;
    public $age_year;

    protected $user_id;

    public function __construct($userID, $config = [])
    {
        $this->user_id = $userID;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['name', 'age_year'], 'required'],
            ['name', 'string', 'max' => 50],
            ['age_year', 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'age_year' => 'Возраст',
        ];
    }

    public function createStudent()
    {
        if (!$this->validate()) {
            throw new RuntimeException('Model not valid');
        }
        $model = UserStudent::create($this->user_id, $this->name, $this->age_year);
        $model->save();
    }

}