<?php

namespace modules\edu\models;

use Yii;

/**
 * This is the model class for table "edu_class_book_student".
 *
 * @property int $class_book_id
 * @property int $student_id
 *
 * @property EduClassBook $classBook
 * @property UserStudent $student
 */
class EduClassBookStudent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'edu_class_book_student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['class_book_id', 'student_id'], 'required'],
            [['class_book_id', 'student_id'], 'integer'],
            [['class_book_id', 'student_id'], 'unique', 'targetAttribute' => ['class_book_id', 'student_id']],
            [['class_book_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduClassBook::className(), 'targetAttribute' => ['class_book_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserStudent::className(), 'targetAttribute' => ['student_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'class_book_id' => 'Class Book ID',
            'student_id' => 'Student ID',
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
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(UserStudent::className(), ['id' => 'student_id']);
    }
}
