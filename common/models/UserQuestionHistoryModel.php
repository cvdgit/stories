<?php

namespace common\models;

use common\models\test\SourceType;
use yii\base\Model;
use yii\db\Query;

class UserQuestionHistoryModel extends Model
{

    public $source;
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
    public $stars;

    public $answers;

    public function rules()
    {
        return [
            [['source', 'student_id', 'test_id', 'entity_id', 'entity_name'], 'required'],
            [['source', 'student_id', 'test_id', 'question_topic_id', 'entity_id', 'relation_id', 'correct_answer'], 'integer'],
            [['question_topic_name', 'entity_name', 'relation_name'], 'string', 'max' => 255],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTest::class, 'targetAttribute' => ['test_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserStudent::class, 'targetAttribute' => ['student_id' => 'id']],
            ['answers', 'safe'],
            ['progress', 'integer', 'min' => 0, 'max' => 100],
            [['stars'], 'integer'],
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
            $this->progress,
            $this->stars
        );
        $model->save();
        return $model->id;
    }

    public function createWordListQuestionHistory()
    {
        if (!$this->validate()) {
            throw new \DomainException('User question history data is not valid');
        }
        $model = UserQuestionHistory::createWordList(
            $this->student_id,
            $this->test_id,
            $this->entity_id,
            $this->entity_name,
            $this->correct_answer,
            $this->progress,
            $this->stars
        );
        $model->save();
        return $model->id;
    }

    public function createUserQuestionAnswers(int $questionAnswerID)
    {
        $models = [];
        if (count($this->answers) > 0) {
            foreach ($this->answers as $answerData) {
                $answerModel = new UserQuestionAnswerModel();
                $answerModel->question_answer_id = $questionAnswerID;
                if ($answerModel->load($answerData, '') && $answerModel->validate()) {
                    $model = $answerModel->createUserQuestionAnswer();
                    $model->save();
                    $models[] = $model;
                }
            }
        }
        return $models;
    }

    public function getUserQuestionHistory(int $testID)
    {
        $query = (new Query())
            ->select([
                't.entity_id AS entityID',
                'MAX(t.id) AS questionID',
            ])
            ->from(['t' => UserQuestionHistory::tableName()])
            ->where('t.student_id = :student', [':student' => $this->student_id])
            ->andWhere('t.test_id = :test', [':test' => $testID])
            ->andWhere('t.correct_answer = 1')
            ->groupBy(['t.entity_id']);
        $leadQuery = (new Query())
            ->select([
                'tbl.entityID AS entity_id',
                'tbl2.relation_id AS relation_id',
                'tbl3.answer_entity_id AS answer_entity_id',
            ])
            ->from(['tbl' => $query])
            ->innerJoin(['tbl2' => UserQuestionHistory::tableName()], 'tbl2.id = tbl.questionID')
            ->innerJoin(['tbl3' => UserQuestionAnswer::tableName()], 'tbl3.question_history_id = tbl2.id')
            ->where('tbl2.stars >= 5');
        return $leadQuery->all();
    }

    public function getUserQuestionHistoryLocal(int $testID)
    {
        $query = (new Query())
            ->select([
                't.entity_id AS entityID',
                'MAX(t.id) AS questionID',
            ])
            ->from(['t' => UserQuestionHistory::tableName()])
            ->where('t.student_id = :student', [':student' => $this->student_id])
            ->andWhere('t.test_id = :test', [':test' => $testID])
            ->andWhere('t.correct_answer = 1')
            ->groupBy(['t.entity_id']);
        $leadQuery = (new Query())
            ->select([
                'tbl.questionID AS question_id',
                'tbl.entityID AS entity_id',
                'tbl2.stars AS stars'
            ])
            ->from(['tbl' => $query])
            ->innerJoin(['tbl2' => UserQuestionHistory::tableName()], 'tbl2.id = tbl.questionID')
            ->having('tbl2.stars >= 5');
        return $leadQuery->all();
    }

    public function getUserQuestionHistoryWithOutRelation(int $testID)
    {
        $query = (new Query())
            ->select(['t.entity_id', 't2.answer_entity_id'])
            ->from(['t' => UserQuestionHistory::tableName()])
            ->innerJoin(['t2' => UserQuestionAnswer::tableName()], 't2.question_history_id = t.id')
            ->where('t.student_id = :student', [':student' => $this->student_id])
            ->andWhere('t.test_id = :test', [':test' => $testID])
            ->andWhere('t.correct_answer = 1')
            ->groupBy(['t.entity_id', 't2.answer_entity_id'])
            ->having('COUNT(t.entity_id) >= 5');
        return $query->all();
    }

    public function getUserHistoryStarsCount(int $testID)
    {
        $stars = $this->getUserQuestionHistoryStars3($testID);
        $ids = [];
        $total = 0;
        foreach ($stars as $star) {
            if (!isset($ids[$star['question_id']])) {
                $total += (int) $star['stars'];
                $ids[$star['question_id']] = $star['question_id'];
            }
        }
        return $total;
    }

    public function getUserHistoryStarsCountLocal(int $testID)
    {
        $stars = $this->getUserQuestionHistoryStarsLocal($testID);
        $ids = [];
        $total = 0;
        foreach ($stars as $star) {
            if (!isset($ids[$star['question_id']])) {
                $total += (int) $star['stars'];
                $ids[$star['question_id']] = $star['question_id'];
            }
        }
        return $total;
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

    public function getUserQuestionHistoryStars3(int $testID)
    {
        $query = (new Query())
            ->select([
                't.entity_id AS entityID',
                't.relation_id AS relationID',
                't2.answer_entity_id AS answerEntityID',
                'MAX(t.created_at) AS maxCreatedAt',
                'MAX(t.id) AS questionID',
            ])
            ->from(['t' => UserQuestionHistory::tableName()])
            ->innerJoin(['t2' => UserQuestionAnswer::tableName()], 't2.question_history_id = t.id')
            ->where('t.student_id = :student', [':student' => $this->student_id])
            ->andWhere('t.test_id = :test', [':test' => $testID])
            ->groupBy(['t2.answer_entity_id', 't.relation_id', 't.entity_id']);
        $leadQuery = (new Query())
            ->select([
                'tbl.questionID AS question_id',
                'tbl.entityID AS entity_id',
                'tbl.relationID AS relation_id',
                'tbl.answerEntityID AS answer_entity_id',
                'tbl2.stars AS stars'
            ])
            ->from(['tbl' => $query])
            ->innerJoin(['tbl2' => UserQuestionHistory::tableName()], 'tbl2.id = tbl.questionID');
        return $leadQuery->all();
    }

    public function getUserQuestionHistoryStarsLocal(int $testID)
    {
        $query = (new Query())
            ->select([
                't.entity_id AS entityID',
                'MAX(t.id) AS questionID',
            ])
            ->from(['t' => UserQuestionHistory::tableName()])
            ->where('t.student_id = :student', [':student' => $this->student_id])
            ->andWhere('t.test_id = :test', [':test' => $testID])
            ->groupBy(['t.entity_id']);
        $leadQuery = (new Query())
            ->select([
                'tbl.questionID AS question_id',
                'tbl.entityID AS entity_id',
                'tbl2.stars AS stars'
            ])
            ->from(['tbl' => $query])
            ->innerJoin(['tbl2' => UserQuestionHistory::tableName()], 'tbl2.id = tbl.questionID');
        return $leadQuery->all();
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

    public function isSourceTest()
    {
        return (int) $this->source === SourceType::TEST;
    }

    public function isSourceWordList()
    {
        return (int) $this->source === SourceType::LIST;
    }

    public function isSourceNeo()
    {
        return (int) $this->source === SourceType::NEO;
    }

    public function getDetail(int $testID)
    {
        $query = (new Query())
            ->select([
                't.entity_id AS entityID',
                't.relation_id AS relationID',
                't2.answer_entity_id AS answerEntityID',
                'MAX(t.id) AS questionID',
            ])
            ->from(['t' => UserQuestionHistory::tableName()])
            ->innerJoin(['t2' => UserQuestionAnswer::tableName()], 't2.question_history_id = t.id')
            ->where('t.student_id = :student', [':student' => $this->student_id])
            ->andWhere('t.test_id = :test', [':test' => $testID])
            ->groupBy(['t2.answer_entity_id', 't.relation_id', 't.entity_id']);
        $leadQuery = (new Query())
            ->select([
                'tbl.questionID AS question_id',
                'tbl2.entity_name AS entity_name',
                '(SELECT t3.answer_entity_name FROM user_question_answer t3 WHERE t3.question_history_id = tbl.questionID AND t3.answer_entity_id = tbl.answerEntityID) AS answer_entity_name',
                'tbl2.stars AS stars'
            ])
            ->from(['tbl' => $query])
            ->innerJoin(['tbl2' => UserQuestionHistory::tableName()], 'tbl2.id = tbl.questionID')
        ->orderBy(['tbl2.created_at' => SORT_ASC]);
        return $leadQuery->all();
    }

}