<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_question_history".
 *
 * @property int $id
 * @property int $student_id
 * @property int $question_topic_id
 * @property string $question_topic_name
 * @property int $entity_id
 * @property string $entity_name
 * @property int $relation_id
 * @property string $relation_name
 * @property int $correct_answer
 * @property int $created_at
 * @property int $test_id
 *
 * @property UserQuestionAnswer[] $userQuestionAnswers
 * @property UserStudent $student
 */
class UserQuestionHistory extends ActiveRecord
{

    public $correct_answers;
    public $max_created_at;
    public $answers;
    public $progress;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_question_history';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['answers', 'progress'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_id' => 'Student ID',
            'test_id' => 'Test ID',
            'question_topic_id' => 'Question Topic ID',
            'question_topic_name' => 'Вопрос',
            'entity_id' => 'Entity ID',
            'entity_name' => 'Сущность вопроса',
            'relation_id' => 'Relation ID',
            'relation_name' => 'Отношение',
            'correct_answer' => 'Ответ верный',
            'created_at' => 'Дата ответа',
            'answers' => 'Ответы',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserQuestionAnswers()
    {
        return $this->hasMany(UserQuestionAnswer::class, ['question_history_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(UserStudent::class, ['id' => 'student_id']);
    }

    public static function create(int $studentID,
                                  int $testID,
                                  int $questionTopicID,
                                  string $questionTopicName,
                                  int $entityID,
                                  string $entityName,
                                  int $relationID,
                                  string $relationName,
                                  int $correctAnswer,
                                  $progress
    ): UserQuestionHistory
    {
        $model = new self;
        $model->student_id = $studentID;
        $model->test_id = $testID;
        $model->question_topic_id = $questionTopicID;
        $model->question_topic_name = $questionTopicName;
        $model->entity_id = $entityID;
        $model->entity_name = $entityName;
        $model->relation_id = $relationID;
        $model->relation_name = $relationName;
        $model->correct_answer = $correctAnswer;
        $model->progress = $progress;
        return $model;
    }

    public function afterSave($insert, $changedAttributes)
    {
        $progressModel = new \frontend\models\StudentQuestionProgress();
        $progressModel->student_id = $this->student_id;
        $progressModel->question_id = $this->question_topic_id;
        $progressModel->test_id = $this->test_id;
        $progressModel->progress = $this->progress;
        $progressModel->updateProgress();
        parent::afterSave($insert, $changedAttributes);
    }

}
