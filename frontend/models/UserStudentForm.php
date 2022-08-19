<?php

namespace frontend\models;

use common\models\UserStudent;
use modules\edu\models\EduClass;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class UserStudentForm extends Model
{

    public $name;
    public $birth_date;
    public $class_id;

    /** @var int|null */
    private $id = null;

    public function __construct(?UserStudent $model = null, $config = [])
    {
        parent::__construct($config);

        if ($model !== null) {
            $this->id = $model->id;
            $this->name = $model->name;
            $this->birth_date = $model->birth_date;
            $this->class_id = $model->class_id;
        }
    }

    public function rules(): array
    {
        return [
            [['name', 'birth_date', 'class_id'], 'required'],
            ['name', 'string', 'max' => 50],
            ['class_id', 'integer'],
            ['birth_date', 'default', 'value' => null],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя',
            'birth_date' => 'Дата рождения',
            'class_id' => 'Класс',
        ];
    }

    public function getClassArray(): array
    {
        return ArrayHelper::map(EduClass::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
    }
}
