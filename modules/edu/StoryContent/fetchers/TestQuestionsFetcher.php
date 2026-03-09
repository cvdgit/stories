<?php

declare(strict_types=1);

namespace modules\edu\StoryContent\fetchers;

use common\models\StoryTestAnswer;
use DateTimeImmutable;
use DateTimeInterface;
use modules\edu\query\GetStoryTests\Slide;
use modules\edu\query\GetStoryTests\SlideTest;
use modules\edu\query\GetStoryTests\StoryStatCollection;
use yii\db\Expression;
use yii\db\Query;

class TestQuestionsFetcher extends AbstractFetcher implements ContentFetcherInterface
{
    /** @var int[] */
    private $testIds;
    /**
     * @var StoryStatCollection|null
     */
    private $collection;

    /**
     * @param array $contentItems list<SlideTest>
     */
    public function __construct(array $contentItems, StoryStatCollection $collection = null)
    {
        $this->testIds = array_map(static function (SlideTest $item): int {
            return $item->getTestId();
        }, $contentItems);
        $this->collection = $collection;
    }

    public function fetch(int $studentId, DateTimeInterface $date = null): int
    {
        $query = (new Query())
            ->select(['id' => new Expression('DISTINCT q.id')])
            ->from(['t' => 'user_question_history'])
            ->innerJoin(['q' => 'story_test_question'], 't.entity_id = q.id')
            ->where(['in', 't.test_id', $this->testIds])
            ->andWhere([
                't.student_id' => $studentId,
                't.correct_answer' => StoryTestAnswer::CORRECT_ANSWER,
            ]);

        if ($date !== null) {
            [$betweenBegin, $betweenEnd] = $this->getBetweenDates($date);
            $query->andWhere(['between', new Expression('t.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd]);

            $rows = $query->all();
            $ids = array_map(
                static function (string $id) use ($date): array {
                    return ['id' => (int) $id, 'date' => $date->format('Y-m-d')];
                },
                array_column($rows, 'id'),
            );
            if ($this->collection !== null && count($ids) > 0) {
                return $this->collection->filterStatRows(Slide::class, $ids);
            }
        }

        $rows = $query->all();
        return count($rows);
    }

    public function fetchRows(int $studentId, DateTimeInterface $date = null): array
    {
        $query = (new Query())
            ->select([
                'id' => 'q.id',
                'date' => new Expression('MAX(t.created_at)'),
            ])
            ->from(['t' => 'user_question_history'])
            ->innerJoin(['q' => 'story_test_question'], 't.entity_id = q.id')
            ->where(['in', 't.test_id', $this->testIds])
            ->andWhere([
                't.student_id' => $studentId,
                't.correct_answer' => StoryTestAnswer::CORRECT_ANSWER,
            ])
            ->groupBy('q.id');

        if ($date !== null) {
            [$betweenBegin, $betweenEnd] = $this->getBetweenDates($date);
            $query->andWhere(['between', new Expression('t.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd]);
        }

        return array_map(
            static function (array $row): array {
                return [
                    'id' => (int) $row['id'],
                    'date' => (new DateTimeImmutable('@' . $row['date']))->format('Y-m-d'),
                ];
            },
            $query->all(),
        );
    }
}
