<?php

declare(strict_types=1);

namespace modules\edu\Teacher\StudentsStat;

use common\components\MentalMapThreshold;
use common\helpers\SmartDate;
use common\models\UserQuestionAnswer;
use common\models\UserQuestionHistory;
use DateTimeInterface;
use modules\edu\query\GetStoryTests\SlideTest;
use modules\edu\query\GetStoryTests\StoryTestsFetcher;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Json;

final class DateStudentStatFetcher
{
    public function fetchMentalMaps(int $userId, int $storyId, DateTimeInterface $targetDate): array
    {
        [$betweenBegin, $betweenEnd] = $this->getBetweenDates($targetDate);
        $query = (new Query())
            ->select('*')
            ->from(['h' => 'mental_map_history'])
            ->where([
                'h.user_id' => $userId,
                'h.story_id' => $storyId,
            ])
            ->andWhere(['between', new Expression('h.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd])
            ->orderBy(['h.created_at' => SORT_ASC]);
        $mentalMapHistoryData = $query->all();

        $mentalMapIds = array_column($mentalMapHistoryData, 'mental_map_id');
        if (count($mentalMapIds) === 0) {
            return [];
        }

        return array_map(
            static function (array $row): array {
                $payload = Json::decode($row['payload'] ?? '[]');
                $userResponse = $payload['user_response'] ?? $row['content'];

                $threshold = $row['threshold'] ?? MentalMapThreshold::getDefaultThreshold(Yii::$app->params);
                $threshold = (int) $threshold;

                $userSimilarity = (int) $row['overall_similarity'];
                $correct = $userSimilarity >= $threshold;

                $allImportantWordsIncluded = $row['all_important_words_included'];
                if ($allImportantWordsIncluded !== null && $correct) {
                    $correct = (int) $allImportantWordsIncluded === 1;
                }
                return [
                    'id' => $row['mental_map_id'],
                    'createdAt' => SmartDate::dateSmart($row['created_at'], true),
                    'userResponse' => $userResponse,
                    'correct' => $correct,
                    'detail' => [
                        'threshold' => $threshold,
                        'userSimilarity' => $userSimilarity,
                    ],
                ];
            },
            $mentalMapHistoryData,
        );
    }

    public function fetchSlides(int $studentId, int $storyId, DateTimeInterface $targetDate): array
    {
        [$betweenBegin, $betweenEnd] = $this->getBetweenDates($targetDate);
        return (new Query())
            ->select('*')
            ->from(['t' => 'story_student_stat'])
            ->where([
                'story_id' => $storyId,
                'student_id' => $studentId,
            ])
            ->andWhere(['between', new Expression('t.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd])
            ->orderBy(['t.created_at' => SORT_ASC])
            ->all();
    }

    public function fetchTestings(int $studentId, int $storyId, DateTimeInterface $targetDate): array
    {
        [$betweenBegin, $betweenEnd] = $this->getBetweenDates($targetDate);

        $slideContent = (new StoryTestsFetcher())->fetch($storyId);
        $storyTests = $slideContent->find(SlideTest::class);

        $testIds = array_map(static function (SlideTest $test): int {
            return $test->getTestId();
        }, $storyTests);

        if (count($testIds) === 0) {
            return [];
        }

        $query = (new Query())
            ->select([
                'h.test_id AS testId',
                'h.created_at AS question_date',
                'h.entity_name AS question_name',
                'h.correct_answer AS correct',
                "GROUP_CONCAT(a.answer_entity_name SEPARATOR ', ') AS user_answers",
            ])
            ->from(['h' => UserQuestionHistory::tableName()])
            ->leftJoin(['a' => UserQuestionAnswer::tableName()], 'h.id = a.question_history_id')
            ->where(['in', 'h.test_id', $testIds])
            ->andWhere(['h.student_id' => $studentId])
            ->andWhere(['between', new Expression('h.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd])
            ->orderBy(['h.created_at' => SORT_ASC])
            ->groupBy('h.id');

        $testingHistoryData = $query->all();

        return array_map(static function (array $row): array {
            return [
                'testId' => (int) $row['testId'],
                'createdAt' => SmartDate::dateSmart($row['question_date'], true),
                'question' => $row['question_name'],
                'answer' => $row['user_answers'],
                'correct' => (int) $row['correct'] === 1,
            ];
        }, $testingHistoryData);
    }

    private function getBetweenDates(DateTimeInterface $date): array
    {
        $targetDate = $date->format('Y-m-d');
        return [
            new Expression("UNIX_TIMESTAMP('$targetDate 00:00:00')"),
            new Expression("UNIX_TIMESTAMP('$targetDate 23:59:59')"),
        ];
    }
}
