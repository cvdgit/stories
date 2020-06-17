<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_question_history".
 *
 * @property int $id
 * @property int $user_id
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
 * @property User $user
 */
class UserQuestionHistory extends \yii\db\ActiveRecord
{

    public $correct_answers;
    public $max_created_at;

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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'slide_id' => 'Slide ID',
            'question_topic_id' => 'Question Topic ID',
            'question_topic_name' => 'Вопрос',
            'entity_id' => 'Entity ID',
            'entity_name' => 'Сущность',
            'relation_id' => 'Relation ID',
            'relation_name' => 'Отношение',
            'correct_answer' => 'Correct Answer',
            'created_at' => 'Created At',
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
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function create(int $userID,
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
        $model->user_id = $userID;
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
