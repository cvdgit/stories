<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_question_history".
 *
 * @property int $id
 * @property int $student_id
 * @property int $slide_id
 * @property int $question_topic_id
 * @property string $question_topic_name
 * @property int $entity_id
 * @property string $entity_name
 * @property int $relation_id
 * @property string $relation_name
 * @property int $correct_answer
 * @property int $created_at
 *
 * @property StorySlide $slide
 * @property UserStudent $student
 * @property UserQuestionAnswer[] $userQuestionAnswers
 */
class UserQuestionHistory extends ActiveRecord
{

    public $correct_answers;
    public $max_created_at;
    public $answers;

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
            ['created_at', 'integer'],
            ['answers', 'safe'],
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
            'slide_id' => 'Slide ID',
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
    public function getSlide()
    {
        return $this->hasOne(StorySlide::class, ['id' => 'slide_id']);
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
    public function getUserQuestionAnswers()
    {
        return $this->hasMany(UserQuestionAnswer::class, ['question_history_id' => 'id']);
    }

    public static function create(int $studentID,
                                  int $slideID,
                                  int $questionTopicID,
                                  string $questionTopicName,
                                  int $entityID,
                                  string $entityName,
                                  int $relationID,
                                  string $relationName,
                                  int $correctAnswer): UserQuestionHistory
    {
        $model = new self;
        $model->student_id = $studentID;
        $model->slide_id = $slideID;
        $model->question_topic_id = $questionTopicID;
        $model->question_topic_name = $questionTopicName;
        $model->entity_id = $entityID;
        $model->entity_name = $entityName;
        $model->relation_id = $relationID;
        $model->relation_name = $relationName;
        $model->correct_answer = $correctAnswer;
        return $model;
    }

}
