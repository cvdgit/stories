<?php

namespace common\models;

use yii\base\Model;
use yii\db\Query;

class UserQuestionHistoryModel extends Model
{

    public $student_id;
    public $slide_id;
    public $question_topic_id;
    public $question_topic_name;
    public $entity_id;
    public $entity_name;
    public $relation_id;
    public $relation_name;
    public $correct_answer;

    public $answers;

    public function __construct($studentID, $config = [])
    {
        $this->student_id = $studentID;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['student_id', 'slide_id', 'question_topic_id', 'question_topic_name', 'entity_id', 'entity_name', 'relation_id', 'relation_name'], 'required'],
            [['student_id', 'slide_id', 'question_topic_id', 'entity_id', 'relation_id', 'correct_answer'], 'integer'],
            [['question_topic_name', 'entity_name', 'relation_name'], 'string', 'max' => 255],
            [['slide_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorySlide::class, 'targetAttribute' => ['slide_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserStudent::class, 'targetAttribute' => ['student_id' => 'id']],
            ['answers', 'safe'],
        ];
    }

    public function createUserQuestionHistory()
    {
        if (!$this->validate()) {
            throw new \DomainException('User question history data is not valid');
        }
        $model = UserQuestionHistory::create(
            $this->student_id,
            $this->slide_id,
            $this->question_topic_id,
            $this->question_topic_name,
            $this->entity_id,
            $this->entity_name,
            $this->relation_id,
            $this->relation_name,
            $this->correct_answer
        );
        $model->save();
        return $model->id;
    }

    public function createUserQuestionAnswers(int $questionAnswerID)
    {
        if (count($this->answers) > 0) {
            foreach ($this->answers as $answerData) {
                $answerModel = new UserQuestionAnswerModel();
                $answerModel->question_answer_id = $questionAnswerID;
                if ($answerModel->load($answerData, '') && $answerModel->validate()) {
                    $answerModel->createUserQuestionAnswer();
                }
            }
        }
    }

    public function getUserQuestionHistory(int $topicID)
    {
        return (new Query())
            ->select(['t.entity_id', 't.relation_id', 't2.answer_entity_id'])
            ->from(['t' => UserQuestionHistory::tableName()])
            ->innerJoin(['t2' => UserQuestionAnswer::tableName()], 't2.question_history_id = t.id')
            ->where('t.student_id = :student', [':student' => $this->student_id])
            ->andWhere('t.question_topic_id = :topic', [':topic' => $topicID])
            ->andWhere('t.correct_answer = 1')
            ->groupBy(['t.entity_id', 't.relation_id', 't2.answer_entity_id'])
            ->having('COUNT(t.entity_id) >= 5')
            ->all();
    }

    public function getUserQuestionHistoryStars(int $topicID)
    {
        return (new Query())
            ->select(['t.entity_id', 't.relation_id', 't2.answer_entity_id', 'COUNT(t.entity_id) AS stars'])
            ->from(['t' => UserQuestionHistory::tableName()])
            ->innerJoin(['t2' => UserQuestionAnswer::tableName()], 't2.question_history_id = t.id')
            ->where('t.student_id = :student', [':student' => $this->student_id])
            ->andWhere('t.question_topic_id = :topic', [':topic' => $topicID])
            ->andWhere('t.correct_answer = 1')
            ->groupBy(['t.entity_id', 't.relation_id', 't2.answer_entity_id'])
            ->having('COUNT(t.entity_id) < 5')
            ->indexBy(['entity_id'])
            ->all();
    }

}