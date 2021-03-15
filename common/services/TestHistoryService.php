<?php

namespace common\services;

use common\models\StoryTest;
use common\models\StudentQuestionProgress;
use common\models\UserQuestionHistory;
use Yii;
use yii\db\Query;

class TestHistoryService
{

    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function getTestBySource(int $source)
    {
        $query = (new Query())
            ->select('id')
            ->from(StoryTest::tableName())
            ->where('source = :source', [':source' => $source])
            ->indexBy('id');
        return array_keys($query->all());
    }

    public function getRecordsCountBySource(int $source): int
    {
        $ids = $this->getTestBySource($source);
        $query = (new Query())
            ->from(UserQuestionHistory::tableName())
            ->where(['in', 'test_id', $ids]);
        return $query->count();
    }

    private function deleteBySource(array $ids)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(UserQuestionHistory::tableName(), ['in', 'test_id', $ids]);
        $command->execute();
    }

    private function resetProgressBySource(array $ids)
    {
        $command = Yii::$app->db->createCommand();
        $command->update(StudentQuestionProgress::tableName(), ['progress' => 0], ['in', 'test_id', $ids]);
        $command->execute();
    }

    public function clearBySource(int $source)
    {
        $this->transactionManager->wrap(function() use ($source) {
            $testIDs = $this->getTestBySource($source);
            if (count($testIDs) > 0) {
                $this->deleteBySource($testIDs);
                $this->resetProgressBySource($testIDs);
            }
        });
    }

}