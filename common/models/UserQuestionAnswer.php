<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_question_answer".
 *
 * @property int $id
 * @property int $question_history_id
 * @property int $answer_entity_id
 * @property string $answer_entity_name
 *
 * @property UserQuestionHistory $questionHistory
 */
class UserQuestionAnswer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_question_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_history_id', 'answer_entity_id', 'answer_entity_name'], 'required'],
            [['question_history_id', 'answer_entity_id'], 'integer'],
            [['answer_entity_name'], 'string', 'max' => 255],
            [['question_history_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserQuestionHistory::className(), 'targetAttribute' => ['question_history_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_history_id' => 'Question History ID',
            'answer_entity_id' => 'Answer Entity ID',
            'answer_entity_name' => 'Answer Entity Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionHistory()
    {
        return $this->hasOne(UserQuestionHistory::class, ['id' => 'question_history_id']);
    }

    public static function create(int $questionHistoryID, int $answerEntityID, string $answerEntityName): UserQuestionAnswer
    {
        $model = new self();
        $model->question_history_id = $questionHistoryID;
        $model->answer_entity_id = $answerEntityID;
        $model->answer_entity_name = $answerEntityName;
        return $model;
    }

}
