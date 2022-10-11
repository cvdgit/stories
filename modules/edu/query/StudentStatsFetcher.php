<?php

declare(strict_types=1);

namespace modules\edu\query;

class StudentStatsFetcher
{
    public function fetch(array $statData, array $programStoriesData, array $storyModels): array
    {
        $stat = [];

        foreach ($statData as $statItem) {

            $item = [
                'date' => $statItem['targetDate'],
                'topics' => [],
            ];

            $topics = [];
            foreach (explode(',', $statItem['storyIds']) as $storyId) {

                $storyData = $programStoriesData[$storyId];

                if (!isset($topics[$storyData['topicId']])) {
                    $topics[$storyData['topicId']] = [
                        'topicId' => $storyData['topicId'],
                        'topicName' => $storyData['topicName'],
                        'lessons' => [],
                    ];
                }

                $topicLessonIds = array_column($topics[$storyData['topicId']]['lessons'],'lessonId');
                if (!in_array($storyData['lessonId'], $topicLessonIds, true)) {
                    $lessonItem = [
                        'lessonId' => $storyData['lessonId'],
                        'lessonName' => $storyData['lessonName'],
                        'stories' => [],
                    ];
                    $topics[$storyData['topicId']]['lessons'][$storyData['lessonId']] = $lessonItem;
                }

                $topics[$storyData['topicId']]['lessons'][$storyData['lessonId']]['stories'][] = $storyModels[$storyId];
            }

            $item['topics'] = $topics;

            $stat[] = $item;
        }

        return $stat;
    }
}
