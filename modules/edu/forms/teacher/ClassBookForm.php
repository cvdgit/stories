<?php

declare(strict_types=1);


namespace modules\edu\forms\teacher;

use modules\edu\models\EduClass;
use modules\edu\models\EduClassBook;
use modules\edu\models\EduProgram;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class ClassBookForm extends Model
{

    public $name;
    public $class_id;
    public $programs = [];

    /** @var null|int */
    private $id = null;

    public function __construct(?EduClassBook $model = null, $config = [])
    {
        parent::__construct($config);
        if ($model !== null) {
            $this->id = $model->id;
            $this->name = $model->name;
            $this->class_id = $model->class_id;
            $this->programs = $model->getProgramIds();
        }
    }

    public function rules(): array
    {
        return [
            [['name', 'class_id'], 'required'],
            ['name', 'string', 'max' => 50],
            ['class_id', 'integer'],
            ['programs', 'each', 'rule' => ['integer']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Название',
            'class_id' => 'Класс',
            'programs' => 'Предметы',
        ];
    }

    public function getClassArray(): array
    {
        return ArrayHelper::map(EduClass::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name');
    }

    public function getProgramArray(): array
    {
        return ArrayHelper::map(EduProgram::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name');
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
