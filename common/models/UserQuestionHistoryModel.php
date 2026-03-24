<?php

declare(strict_types=1);

namespace common\models;

use common\models\test\SourceType;
use DomainException;
use yii\base\Model;
use yii\db\Query;

class UserQuestionHistoryModel extends Model
{
    public const LOCATION_EDUCATION = 'education';

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
    public $location;

    public $answers;

    public function rules(): array
    {
        return [
            [['source', 'student_id', 'test_id', 'entity_id', 'entity_name'], 'required'],
            [
                ['source', 'student_id', 'test_id', 'question_topic_id', 'entity_id', 'relation_id', 'correct_answer'],
                'integer',
            ],
            [['question_topic_name', 'relation_name'], 'string', 'max' => 255],
            [['entity_name'], 'string', 'max' => 512],
            [
                ['test_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => StoryTest::class,
                'targetAttribute' => ['test_id' => 'id'],
            ],
            [
                ['student_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => UserStudent::class,
                'targetAttribute' => ['student_id' => 'id'],
            ],
            ['answers', 'safe'],
            ['progress', 'integer', 'min' => 0, 'max' => 100],
            [['stars'], 'integer'],
            ['location', 'string', 'max' => 10],
        ];
    }

    public function createUserQuestionHistory(): int
    {
        if (!$this->validate()) {
            throw new DomainException('User question history data is not valid');
        }
        $model = UserQuestionHistory::create(
            (int) $this->student_id,
            (int) $this->test_id,
            (int) $this->question_topic_id,
            $this->question_topic_name,
            (int) $this->entity_id,
            $this->entity_name,
            (int) $this->relation_id,
            $this->relation_name,
            (int) $this->correct_answer,
            $this->progress,
            $this->stars,
            $this->location,
        );
        if (!$model->save()) {
            throw new DomainException('User question history save error');
        }
        return $model->id;
    }

    public function createWordListQuestionHistory(): int
    {
        if (!$this->validate()) {
            throw new DomainException('User question history data is not valid');
        }
        $model = UserQuestionHistory::createWordList(
            (int) $this->student_id,
            (int) $this->test_id,
            (int) $this->entity_id,
            $this->entity_name,
            (int) $this->correct_answer,
            $this->progress,
            $this->stars,
            $this->location,
        );
        if (!$model->save()) {
            throw new DomainException('User question history save error');
        }
        return $model->id;
    }

    public function createUserQuestionAnswers(int $questionAnswerID): array
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

    public function getUserQuestionHistory(int $testID): array
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

    public function getUserQuestionHistoryLocal(int $testID, int $repeat): array
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
                'tbl2.stars AS stars',
            ])
            ->from(['tbl' => $query])
            ->innerJoin(['tbl2' => UserQuestionHistory::tableName()], 'tbl2.id = tbl.questionID')
            ->having('tbl2.stars >= ' . $repeat);
        return $leadQuery->all();
    }

    public function getUserQuestionHistoryWithOutRelation(int $testID): array
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

    public function getUserHistoryStarsCount(int $testID): int
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

    public function getUserHistoryStarsCountLocal(int $testID): int
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

    public function getUserQuestionHistoryStars2(int $testID): array
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

    public function getUserQuestionHistoryStars3(int $testID): array
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
                'tbl2.stars AS stars',
            ])
            ->from(['tbl' => $query])
            ->innerJoin(['tbl2' => UserQuestionHistory::tableName()], 'tbl2.id = tbl.questionID');
        return $leadQuery->all();
    }

    public function getUserQuestionHistoryStarsLocal(int $testID): array
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
                'tbl2.stars AS stars',
            ])
            ->from(['tbl' => $query])
            ->innerJoin(['tbl2' => UserQuestionHistory::tableName()], 'tbl2.id = tbl.questionID');
        return $leadQuery->all();
    }

    public function getUserContinentsData(int $testID): array
    {
        $subQuery = (new Query())
            ->select(
                [
                    't.entity_name AS entityName',
                    't.relation_name AS relationName',
                    't2.answer_entity_name AS answerEntityName',
                ],
            )
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

    public function getUserAnimalsData(int $testID): array
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

    public function isSourceTest(): bool
    {
        return (int) $this->source === SourceType::TEST;
    }

    public function isSourceTests(): bool
    {
        return (int) $this->source === SourceType::TESTS;
    }

    public function isSourceWordList(): bool
    {
        return (int) $this->source === SourceType::LIST;
    }

    public function isSourceNeo(): bool
    {
        return (int) $this->source === SourceType::NEO;
    }

    public function getDetail(int $testID): array
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
                'tbl2.stars AS stars',
                'tbl2.created_at AS question_date',
            ])
            ->from(['tbl' => $query])
            ->innerJoin(['tbl2' => UserQuestionHistory::tableName()], 'tbl2.id = tbl.questionID')
            ->orderBy(['tbl2.created_at' => SORT_ASC]);
        return $leadQuery->all();
    }
}
