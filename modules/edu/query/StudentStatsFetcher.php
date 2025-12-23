<?php

declare(strict_types=1);

namespace modules\edu\query;

use modules\edu\components\ArrayHelper;
use yii\helpers\Json;

class StudentStatsFetcher
{
    private function createTopics(array $programStoriesData): array
    {
        $topics = [];
        foreach ($programStoriesData as $dataItem) {
            $topicId = $dataItem['topicId'];
            $topic = $topics[$topicId] ?? null;
            if ($topic === null) {
                $topic = Topic::fromPayload([
                    'topicId' => $topicId,
                    'topicName' => $dataItem['topicName'],
                    'lessons' => [],
                ]);
            }
            $lessonId = $dataItem['lessonId'];
            $lesson = $topic->findLesson((int) $lessonId);
            if ($lesson === null) {
                $topic->addLesson(Lesson::fromPayload([
                    'lessonId' => $lessonId,
                    'lessonName' => $dataItem['lessonName'],
                    'stories' => [],
                ]));
            }
            $topics[$topicId] = $topic;
        }
        return array_values($topics);
    }

    public function fetch(array $statData, array $programStoriesData): array
    {
        $stat = [];

        foreach ($statData as $statItem) {

            $topics = $this->createTopics($programStoriesData);

            foreach (explode(',', $statItem['storyIds']) as $storyId) {
                $dataRows = array_values(
                    array_filter($programStoriesData, static function ($elem) use ($storyId) {
                        return (int) $elem['storyId'] === (int) $storyId;
                    }),
                );

                foreach ($dataRows as $dataRow) {
                    $topic = ArrayHelper::array_find($topics, static function(Topic $topic) use ($dataRow): bool {
                        return $topic->getId() === (int) $dataRow['topicId'];
                    });
                    if ($topic === null) {
                        continue;
                    }
                    $lesson = $topic->findLesson((int) $dataRow['lessonId']);
                    if ($lesson === null) {
                        continue;
                    }
                    $lesson->addStoryId((int) $storyId);
                }
            }

            $topics = array_map(static function (Topic $topic): Topic {
                $topic->setLessons($topic->getLessonsWithStories());
                return $topic;
            }, $topics);

            $topics = array_values(
                array_filter($topics, static function (Topic $topic): bool {
                    return $topic->haveLessons();
                }),
            );

            $item = [
                'date' => $statItem['targetDate'],
                'topics' => Json::decode(
                    Json::encode($topics)
                ),
            ];
            $stat[] = $item;
        }

        return $stat;
    }
}
