<?php

namespace backend\models;

use common\models\StoryTest;
use common\models\StudentQuestionProgress;
use common\models\UserQuestionHistory;
use Yii;
use yii\base\Model;
use yii\db\Query;

class StudentTestHistory extends Model
{

    public $studentID;

    public function __construct(int $studentID, $config = [])
    {
        $this->studentID = $studentID;
        parent::__construct($config);
    }

    public function getStudentTests(): array
    {
        $query = (new Query())
            ->select(['t.test_id', 't2.title'])
            ->distinct()
            ->from(['t' => UserQuestionHistory::tableName()])
            ->innerJoin(['t2' => StoryTest::tableName()], 't.test_id = t2.id')
            ->where('t.student_id = :student', [':student' => $this->studentID]);
        return $query->all();
    }

    public function getStudentTestHistoryCount(int $testID)
    {
        $query = (new Query())
            ->from(UserQuestionHistory::tableName())
            ->where('student_id = :student', [':student' => $this->studentID])
            ->andWhere('test_id = :test', [':test' => $testID]);
        return $query->count();
    }

    public function clearTestHistory(int $testID)
    {
        UserQuestionHistory::clearTestHistory($this->studentID, $testID);
        StudentQuestionProgress::resetProgress($this->studentID, $testID);
    }

}