<?php

namespace modules\edu\models;

use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "edu_class_program".
 *
 * @property int $id
 * @property int $class_id
 * @property int $program_id
 *
 * @property EduClass $class
 * @property EduProgram $program
 */
class EduClassProgram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'edu_class_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['class_id', 'program_id'], 'required'],
            [['class_id', 'program_id'], 'integer'],
            [['class_id', 'program_id'], 'unique', 'targetAttribute' => ['class_id', 'program_id']],
            [['class_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduClass::className(), 'targetAttribute' => ['class_id' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduProgram::className(), 'targetAttribute' => ['program_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'class_id' => 'Класс',
            'program_id' => 'Предмет',
        ];
    }

    public function getClass(): ActiveQuery
    {
        return $this->hasOne(EduClass::class, ['id' => 'class_id']);
    }

    public function getProgram(): ActiveQuery
    {
        return $this->hasOne(EduProgram::class, ['id' => 'program_id']);
    }

    public function getClassArray(): array
    {
        return ArrayHelper::map(EduClass::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name');
    }

    public function getProgramArray(): array
    {
        return ArrayHelper::map(EduProgram::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name');
    }

    public function getEduPath(): string
    {
        return $this->class->name . ' - ' . $this->program->name;
    }
}
