<?php

namespace console\controllers;

use common\models\StoryTest;
use common\models\UserQuestionAnswer;
use common\models\UserQuestionHistory;
use common\models\UserQuestionHistoryModel;
use Yii;
use yii\console\Controller;
use yii\db\Expression;
use yii\db\Query;

class HistoryController extends Controller
{

/*    public function actionClear()
    {
        $command = Yii::$app->db->createCommand();
        $command->update('{{%story}}', ['views_number' => 0]);
        $command->execute();

        $command = Yii::$app->db->createCommand();
        $command->truncateTable('{{%story_statistics}}');
        $command->execute();

        $this->stdout('Done!' . PHP_EOL);
    }*/

    public function actionUpdateStars()
    {
        $query = (new Query())
            ->select([
                't.test_id',
                new Expression('(SELECT t2.title FROM story_test t2 WHERE t2.id = t.test_id) AS test_title'),
                't.student_id',
                new Expression('(SELECT t3.name FROM user_student t3 WHERE t3.id = t.student_id) AS student_name'),
            ])
            ->from(['t' => UserQuestionHistory::tableName()])
            ->where('t.stars = 0')
            ->groupBy(['t.test_id', 't.student_id']);

        foreach ($query->each() as $row) {

            $this->stdout($row['test_title'] . PHP_EOL);
            $this->stdout($row['student_name'] . PHP_EOL);

            $testID = $row['test_id'];
            $studentID = $row['student_id'];
            $data = $this->getStudentTestData($testID, $studentID);
            foreach ($data as $dataRow) {
                $stars = (int) $dataRow['correct_answers'];
                if ($stars > 5) {
                    $stars = 5;
                }
                $result = $this->updateHistoryStars($dataRow['question_id'], $stars);
                $this->stdout(var_export($result, true) . PHP_EOL);
            }

            $this->stdout('------' . PHP_EOL);
        }
        $this->stdout('Done!' . PHP_EOL);
    }

    private function getStudentTestData(int $testID, int $studentID)
    {
        $query = (new Query())
            ->select(['MAX(t.id) AS question_id', 'SUM(t.correct_answer) AS correct_answers'])
            ->from(['t' => UserQuestionHistory::tableName()])
            ->innerJoin(['t2' => UserQuestionAnswer::tableName()], 't2.question_history_id = t.id')
            ->where('t.test_id = :test', [':test' => $testID])
            ->andWhere('t.student_id = :student', [':student' => $studentID])
            ->groupBy(['t2.answer_entity_id', 't.relation_id', 't.entity_id']);
        return $query->all();
    }

    private function updateHistoryStars(int $questionID, int $stars)
    {
        $command = Yii::$app->db->createCommand();
        $command->update(UserQuestionHistory::tableName(), ['stars' => $stars], 'id = :id', [':id' => $questionID]);
        return $command->execute();
    }

}