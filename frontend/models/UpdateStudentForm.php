<?php

namespace frontend\models;

use common\models\UserStudent;
use yii\base\Model;

class UpdateStudentForm extends Model
{

    public $name;
    public $birth_date;

    /** @var UserStudent */
    private $model;

    public function __construct(int $studentID, $config = [])
    {
        $this->loadModel($studentID);
        $this->loadModelValues();
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

    private function loadModel(int $id)
    {
        if ($this->model === null) {
            $this->model = UserStudent::findModel($id);
        }
        return $this->model;
    }

    public function userOwned(int $userID)
    {
        return $this->model->userOwned($userID);
    }

    public function getModelID()
    {
        return $this->model->id;
    }

    private function loadModelValues()
    {
        $this->name = $this->model->name;
        $this->birth_date = $this->model->birth_date;
    }

    public function updateStudent()
    {
        if (!$this->validate()) {
            throw new \RuntimeException('Model not valid');
        }
        $this->model->name = $this->name;
        $this->model->birth_date = $this->birth_date;
        $this->model->save();
    }

}