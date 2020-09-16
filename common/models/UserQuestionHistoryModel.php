<?php

namespace common\models;

use yii\base\Model;
use yii\db\Query;

class UserQuestionHistoryModel extends Model
{

    public $student_id;
    public $question_topic_id;
    public $question_topic_name;
    public $entity_id;
    public $entity_name;
    public $relation_id;
    public $relation_name;
    public $correct_answer;
    public $progress;
    public $test_id;

    public $answers;

    public function rules()
    {
        return [
            [['student_id', 'test_id', 'question_topic_id', 'question_topic_name', 'entity_id', 'entity_name', 'relation_id', 'relation_name'], 'required'],
            [['student_id', 'test_id', 'question_topic_id', 'entity_id', 'relation_id', 'correct_answer'], 'integer'],
            [['question_topic_name', 'entity_name', 'relation_name'], 'string', 'max' => 255],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTest::class, 'targetAttribute' => ['test_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserStudent::class, 'targetAttribute' => ['student_id' => 'id']],
            ['answers', 'safe'],
            ['progress', 'integer', 'min' => 0, 'max' => 100]
        ];
    }

    public function createUserQuestionHistory()
    {
        if (!$this->validate()) {
            throw new \DomainException('User question history data is not valid');
        }
        $model = UserQuestionHistory::create(
            $this->student_id,
            $this->test_id,
            $this->question_topic_id,
            $this->question_topic_name,
            $this->entity_id,
            $this->entity_name,
            $this->relation_id,
            $this->relation_name,
            $this->correct_answer,
            $this->progress
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

    public function getUserQuestionHistory(int $testID)
    {
        $query = (new Query())
            ->select(['t.entity_id', 't.relation_id', 't2.answer_entity_id'])
            ->from(['t' => UserQuestionHistory::tableName()])
            ->innerJoin(['t2' => UserQuestionAnswer::tableName()], 't2.question_history_id = t.id')
            ->where('t.student_id = :student', [':student' => $this->student_id])
            ->andWhere('t.test_id = :test', [':test' => $testID])
            ->andWhere('t.correct_answer = 1')
            ->groupBy(['t.entity_id', 't.relation_id', 't2.answer_entity_id'])
            ->having('COUNT(t.entity_id) >= 5');
        return $query->all();
    }

    public function getUserHistoryStarsCount(int $testID)
    {
        $query = (new Query())
            ->select(['t.id'])
            ->from(['t' => UserQuestionHistory::tableName()])
            ->innerJoin(['t2' => UserQuestionAnswer::tableName()], 't2.question_history_id = t.id')
            ->where('t.student_id = :student', [':student' => $this->student_id])
            ->andWhere('t.test_id = :test', [':test' => $testID])
            ->andWhere('t.correct_answer = 1')
            ->groupBy(['t.id']);
        return $query->count();
    }

    public function getUserQuestionHistoryStars2(int $testID)
    {
        return (new Query())
            ->select(['t.entity_id', 't.relation_id', 't2.answer_entity_id', 'COUNT(t2.answer_entity_id) AS stars'])
            ->from(['t' => UserQuestionHistory::tableName()])
            ->innerJoin(['t2' => UserQuestionAnswer::tableName()], 't2.question_history_id = t.id')
            ->where('t.student_id = :student', [':student' => $this->student_id])
            ->andWhere('t.test_id = :test', [':test' => $testID])
            ->andWhere('t.correct_answer = 1')
            ->groupBy(['t2.answer_entity_id', 't.relation_id', 't.entity_id'])
            ->having('COUNT(t2.answer_entity_id) < 5')
            ->all();
    }

    public function getUserContinentsData(int $testID)
    {
        $subQuery = (new Query())
            ->select(['t.entity_name AS entityName', 't.relation_name AS relationName', 't2.answer_entity_name AS answerEntityName'])
            ->from(['t' => UserQuestionHistory::tableName()])
            ->innerJoin(['t2' => UserQuestionAnswer::tableName()], 't2.question_history_id = t.id')
            ->where('t.student_id = :student', [':student' => $this->student_id])
            ->andWhere('t.test_id = :test', [':test' => $testID])
            ->andWhere('t.correct_answer = 1')
            ->groupBy(['t.entity_name', 't.relation_name', 't2.answer_entity_name'])
            ->having('COUNT(t.entity_name) >= 5');
        $query = (new Query())
            ->select(['q.entityName', 'COUNT(q.answerEntityName) AS number_animals'])
            ->from(['q' => $subQuery])
            ->groupBy(['q.entityName']);
        return $query->all();
    }

    public function getUserAnimalsData(int $testID)
    {
        $query = (new Query())
            ->select(['t.entity_name', 't.relation_name', 't2.answer_entity_name'])
            ->from(['t' => UserQuestionHistory::tableName()])
            ->innerJoin(['t2' => UserQuestionAnswer::tableName()], 't2.question_history_id = t.id')
            ->where('t.student_id = :student', [':student' => $this->student_id])
            ->andWhere('t.test_id = :test', [':test' => $testID])
            ->andWhere('t.correct_answer = 1')
            ->groupBy(['t.entity_name', 't.relation_name', 't2.answer_entity_name']);
        return $query->all();
    }

}