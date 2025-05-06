<?php

declare(strict_types=1);

namespace frontend\Training;

use Yii;
use yii\db\Expression;
use yii\db\Query;

class QuizDetailFetcher
{
    public function fetch(int $studentId, int $storyId, string $betweenBegin, string $betweenEnd): array
    {
        $ids = (new Query())
            ->select([
                'testId' => new Expression('DISTINCT t.test_id'),
            ])
            ->from(['t' => 'story_story_test'])
            ->where(['t.story_id' => $storyId])
            ->all();

        if (count($ids) === 0) {
            return [];
        }

        $ids = array_column($ids, 'testId');

        $sql = "
SELECT t.entity_name,
       t.correct_answer,
       (SELECT GROUP_CONCAT(a.answer_entity_id ORDER BY a.id SEPARATOR ',') FROM user_question_answer a WHERE t.id = a.question_history_id) AS user_answers,
       (SELECT GROUP_CONCAT(a.answer_entity_name ORDER BY a.id SEPARATOR ',') FROM user_question_answer a WHERE t.id = a.question_history_id) AS user_answers_text,
       t.created_at,
       t.test_id,
       t.entity_id AS questionId
FROM user_question_history t
INNER JOIN story_story_test t2 ON t.test_id = t2.test_id
INNER JOIN story_test_question q ON t.entity_id = q.id
WHERE
    t.student_id = :studentId
  AND t.test_id IN (" . implode(',', $ids) . ")
AND t.created_at + (3 * 60 * 60) BETWEEN $betweenBegin AND $betweenEnd
ORDER BY t.created_at
";
        $command = Yii::$app->db->createCommand($sql, [
            'studentId' => $studentId,
        ]);
        $rows = $command->queryAll();

        if (count($rows) === 0) {
            return [];
        }

        $quizIds = array_map(static function(array $row): int {
            return (int) $row['test_id'];
        }, $rows);
        $quizIds = array_values(array_unique($quizIds));
        $sql = "
SELECT q.story_test_id, q.name AS 'questionName', q.id AS 'questionId', q.type, a.name AS 'answerName', a.id AS 'answerId', a.is_correct AS 'correct'
FROM story_test_question q
INNER JOIN story_test_answer a ON q.id = a.story_question_id
         WHERE q.story_test_id IN (" . implode(',', $quizIds) . ")";
        $questionsCommand = Yii::$app->db->createCommand($sql);
        $questionRows = $questionsCommand->queryAll();

        $data = [];
        foreach ($questionRows as $questionRow) {
            $questionId = (int) $questionRow['questionId'];
            if (!isset($data[$questionId])) {
                $data[$questionId] = [
                    'test_id' => (int) $questionRow['story_test_id'],
                    'id' => $questionId,
                    'question' => $questionRow['questionName'],
                    'type' => $questionRow['type'],
                    'answers' => [],
                ];
            }

            $data[$questionId]['answers'][] = [
                'id' => $questionRow['answerId'],
                'name' => $questionRow['answerName'],
                'correct' => (int) $questionRow['correct'] === 1,
            ];
        }

        $result = [];
        foreach ($rows as $historyRow) {
            $questionId = $historyRow['questionId'];
            if (isset($data[$questionId])) {
                $dataRow = $data[$questionId];
                $result[] = [
                    'question' => $dataRow['question'],
                    'correct' => (int) $historyRow['correct_answer'] === 1,
                    'answers' => $dataRow['answers'],
                    'type' => (int) $dataRow['type'],
                    'user_answers' => explode(',', (string) $historyRow['user_answers']),
                    //'user_answers_text' => explode(',', $historyRow['user_answers_text']),
                ];
            }
        }

        return $result;
    }
}
