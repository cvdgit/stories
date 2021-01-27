<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "test_remember_answer".
 *
 * @property int $test_id
 * @property int $entity_id
 * @property int $student_id
 * @property string $answer
 *
 * @property UserStudent $student
 * @property StoryTest $test
 */
class TestRememberAnswer extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'test_remember_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['test_id', 'entity_id', 'student_id', 'answer'], 'required'],
            [['test_id', 'entity_id', 'student_id'], 'integer'],
            [['answer'], 'string', 'max' => 255],
            [['test_id', 'entity_id'], 'unique', 'targetAttribute' => ['test_id', 'entity_id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserStudent::class, 'targetAttribute' => ['student_id' => 'id']],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTest::class, 'targetAttribute' => ['test_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'test_id' => 'Test ID',
            'entity_id' => 'Entity ID',
            'student_id' => 'Student ID',
            'answer' => 'Answer',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(UserStudent::class, ['id' => 'student_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(StoryTest::class, ['id' => 'test_id']);
    }

    public static function getTestRememberAnswerData(int $testID, int $studentID)
    {
        return self::find()
            ->where('test_id = :test', [':test' => $testID])
            ->andWhere('student_id = :student', [':student' => $studentID])
            ->asArray()
            ->all();
    }

    public static function create(int $testID, int $studentID, int $entityID, string $answer)
    {
        $model = new self();
        $model->test_id = $testID;
        $model->student_id = $studentID;
        $model->entity_id = $entityID;
        $model->answer = $answer;
        return $model;
    }

    public static function updateTestRememberAnswer(int $testID, int $studentID, int $entityID, string $answer)
    {
        if (self::findOne(['test_id' => $testID, 'student_id' => $studentID, 'entity_id' => $entityID]) === null) {
            $model = self::create($testID, $studentID, $entityID, $answer);
            $model->save();
        }
    }

}
