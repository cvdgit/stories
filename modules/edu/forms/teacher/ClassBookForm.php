<?php

declare(strict_types=1);


namespace modules\edu\forms\teacher;

use modules\edu\models\EduClass;
use modules\edu\models\EduClassBook;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class ClassBookForm extends Model
{

    public $name;
    public $class_id;
    public $class_programs = [];

    /** @var null|int */
    private $id = null;

    public function __construct(?EduClassBook $model = null, $config = [])
    {
        parent::__construct($config);
        if ($model !== null) {
            $this->id = $model->id;
            $this->name = $model->name;
            $this->class_id = $model->class_id;
            $this->class_programs = $model->getClassProgramIds();
        }
    }

    public function rules(): array
    {
        return [
            [['name', 'class_id', 'class_programs'], 'required'],
            ['name', 'string', 'max' => 50],
            ['class_id', 'integer'],
            ['class_programs', 'each', 'rule' => ['integer']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Название',
            'class_id' => 'Класс',
            'class_programs' => 'Предметы',
        ];
    }

    public function getClassArray(): array
    {
        return ArrayHelper::map(EduClass::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name');
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
