<?php

namespace backend\components;

use DomainException;
use Yii;
use yii\db\Query;

class FeedbackPathBuilder
{

    public function build(int $feedbackId): array
    {
        $query = (new Query())
            ->select([
                'story_id' => 'story.id',
                'story_title' => 'story.title',
                'story_alias' => 'story.alias',
                'slide_id' => 'story_slide.id',
                'slide_number' => 'story_slide.number',
                'testing_id' => 'testing.id',
                'testing_title' => 'testing.title',
                'question_id' => 'question.id',
                'question_name' => 'question.name',
            ])
            ->from(['t' => 'story_feedback'])
            ->innerJoin('story', 't.story_id = story.id')
            ->innerJoin('story_slide', 't.slide_id = story_slide.id')
            ->leftJoin(['testing' => 'story_test'], 't.testing_id = testing.id')
            ->leftJoin(['question' => 'story_test_question'], 't.question_id = question.id')
            ->where(['t.id' => $feedbackId]);
        $row = $query->one();

        if (!$row) {
            throw new DomainException('Feedback row not found');
        }

        $pathItems = [];

        $pathItems[] = $this->createPathItem($row['story_title'], Yii::$app->urlManager->createUrl(['story/update', 'id' => $row['story_id']]));
        $pathItems[] = $this->createPathItem(
            'Слайд ' . $row['slide_number'],
            Yii::$app->urlManager->createUrl(['editor/edit', 'id' => $row['story_id'], '#' => $row['slide_id']])
        );

        if (!empty($row['testing_id'])) {
            $pathItems[] = $this->createPathItem(
                $row['testing_title'],
                Yii::$app->urlManager->createUrl(['test/update', 'id' => $row['testing_id']])
            );
        }

        if (!empty($row['question_id'])) {
            $pathItems[] = $this->createPathItem(
                $row['question_name'],
                Yii::$app->urlManager->createUrl(['test/update-question', 'question_id' => $row['question_id']])
            );
        }

        return $pathItems;
    }

    private function createPathItem(string $title, string $url): array
    {
        return [
            'title' => $title,
            'url' => $url,
        ];
    }
}
