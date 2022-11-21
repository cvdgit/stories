<?php

declare(strict_types=1);

namespace modules\edu\query;

class StudentStatsFetcher
{
    public function fetch(array $statData, array $programStoriesData): array
    {
        $stat = [];

        foreach ($statData as $statItem) {

            $item = [
                'date' => $statItem['targetDate'],
                'topics' => [],
            ];

            $topics = [];
            foreach (explode(',', $statItem['storyIds']) as $storyId) {

                $row = array_filter($programStoriesData, static function($elem) use ($storyId) {
                    return (int)$elem['storyId'] === (int)$storyId;
                });
                $storyRow = current($row);

                $topicRows = array_filter($programStoriesData, static function($elem) use ($storyRow) {
                    return (int)$elem['topicId'] === (int)$storyRow['topicId'] && (int)$elem['lessonId'] === (int)$storyRow['lessonId'];
                });

                $lesson = [
                    'lessonName' => $storyRow['lessonName'],
                    'lessonId' => $storyRow['lessonId'],
                    'stories' => array_column($topicRows, 'storyId'),
                ];

                $topics[] = [
                    'topicId' => $storyRow['topicId'],
                    'topicName' => $storyRow['topicName'],
                    'lessons' => [$lesson],
                ];

                $item['topics'] = $topics;
            }

            $stat[] = $item;
        }

        return $stat;
    }
}
