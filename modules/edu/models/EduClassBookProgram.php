<?php

namespace modules\edu\models;

use Yii;

/**
 * This is the model class for table "edu_class_book_program".
 *
 * @property int $class_book_id
 * @property int $program_id
 *
 * @property EduClassBook $classBook
 * @property EduProgram $program
 */
class EduClassBookProgram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'edu_class_book_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['class_book_id', 'program_id'], 'required'],
            [['class_book_id', 'program_id'], 'integer'],
            [['class_book_id', 'program_id'], 'unique', 'targetAttribute' => ['class_book_id', 'program_id']],
            [['class_book_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduClassBook::className(), 'targetAttribute' => ['class_book_id' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduProgram::className(), 'targetAttribute' => ['program_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'class_book_id' => 'Class Book ID',
            'program_id' => 'Program ID',
        ];
    }

    /**
     * Gets query for [[ClassBook]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClassBook()
    {
        return $this->hasOne(EduClassBook::className(), ['id' => 'class_book_id']);
    }

    /**
     * Gets query for [[Program]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(EduProgram::className(), ['id' => 'program_id']);
    }
}
