<?php

declare(strict_types=1);

namespace modules\edu\query;

use yii\db\Expression;
use yii\db\Query;

class StudentQuestionFetcher
{
    public function fetch(int $studentId, int $storyId, string $targetDate): array
    {
        $betweenBegin = new Expression("UNIX_TIMESTAMP('$targetDate 00:00:00')");
        $betweenEnd = new Expression("UNIX_TIMESTAMP('$targetDate 23:59:59')");

        $query = (new Query())
            ->from('story')
            ->innerJoin('story_story_test', 'story_story_test.story_id = story.id')
            ->innerJoin('user_question_history', 'story_story_test.test_id = user_question_history.test_id')
            ->where(['story.id' => $storyId])
            ->andWhere(['user_question_history.student_id' => $studentId])
            ->andWhere(['between', 'user_question_history.created_at', $betweenBegin, $betweenEnd]);

        $incorrectQuery = clone $query;
        $incorrectQuery->andWhere('user_question_history.correct_answer = 0');

        return [
            'total' => $query->count('user_question_history.id'),
            'incorrect' => $incorrectQuery->count(),
        ];
    }
}
