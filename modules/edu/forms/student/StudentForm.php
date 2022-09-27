<?php

declare(strict_types=1);


namespace modules\edu\forms\student;

use common\models\UserStudent;
use modules\edu\models\EduClass;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class StudentForm extends Model
{

    public $name;
    public $class_id;

    public function __construct(UserStudent $model = null, $config = [])
    {
        parent::__construct($config);
        if ($model !== null) {
            $this->name = $model->name;
            $this->class_id = $model->class_id;
        }
    }

    public function rules(): array
    {
        return [
            [['name', 'class_id'], 'required'],
            ['name', 'string', 'max' => 50],
            ['class_id', 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя',
            'class_id' => 'Класс',
        ];
    }

    public function getClassArray(): array
    {
        return ArrayHelper::map(EduClass::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name');
    }
}
