<?php

namespace modules\edu\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "edu_class_book_program".
 *
 * @property int $class_book_id
 * @property int $class_program_id
 *
 * @property EduClassBook $classBook
 * @property EduClassProgram $classProgram
 */
class EduClassBookProgram extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'edu_class_book_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['class_book_id', 'class_program_id'], 'required'],
            [['class_book_id', 'class_program_id'], 'integer'],
            [['class_book_id', 'class_program_id'], 'unique', 'targetAttribute' => ['class_book_id', 'class_program_id']],
            [['class_book_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduClassBook::class, 'targetAttribute' => ['class_book_id' => 'id']],
            [['class_program_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduProgram::class, 'targetAttribute' => ['class_program_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'class_book_id' => 'Class Book ID',
            'class_program_id' => 'Program ID',
        ];
    }

    public function getClassBook(): ActiveQuery
    {
        return $this->hasOne(EduClassBook::class, ['id' => 'class_book_id']);
    }

    public function getClassProgram(): ActiveQuery
    {
        return $this->hasOne(EduClassProgram::class, ['id' => 'class_program_id']);
    }
}
