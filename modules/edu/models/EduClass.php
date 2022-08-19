<?php

namespace modules\edu\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "edu_class".
 *
 * @property int $id
 * @property string $name
 *
 * @property EduClassBook[] $eduClassBooks
 * @property EduClassProgram[] $eduClassPrograms
 * @property EduProgram[] $programs
 */
class EduClass extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'edu_class';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
        ];
    }

    public function getEduClassBooks(): ActiveQuery
    {
        return $this->hasMany(EduClassBook::class, ['class_id' => 'id']);
    }

    public function getEduClassPrograms(): ActiveQuery
    {
        return $this->hasMany(EduClassProgram::class, ['class_id' => 'id']);
    }

    public function getPrograms(): ActiveQuery
    {
        return $this->hasMany(EduProgram::class, ['id' => 'program_id'])
            ->viaTable('edu_class_program', ['class_id' => 'id']);
    }
}
