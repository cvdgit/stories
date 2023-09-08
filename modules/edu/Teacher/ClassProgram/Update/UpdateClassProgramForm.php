<?php

namespace modules\edu\Teacher\ClassProgram\Update;

use modules\edu\models\EduClassProgram;
use yii\base\Model;

class UpdateClassProgramForm extends Model
{
    public $class_id;
    public $program_id;

    public function __construct(EduClassProgram $model, $config = [])
    {
        parent::__construct($config);
        $this->class_id = $model->class_id;
        $this->program_id = $model->program_id;
    }

    public function rules(): array
    {
        return [
            [['class_id', 'program_id'], 'required'],
            [['class_id', 'program_id'], 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'class_id' => 'Класс',
            'program_id' => 'Программа',
        ];
    }
}
