<?php

declare(strict_types=1);

namespace modules\edu\StoryContent\fetchers;

use DateTimeImmutable;
use DateTimeInterface;
use modules\edu\query\GetStoryTests\Slide;
use modules\edu\query\GetStoryTests\StoryStatCollection;
use yii\db\Expression;
use yii\db\Query;

class SlidesFetcher extends AbstractFetcher implements ContentFetcherInterface
{
    /** @var int[] */
    private $slideIds;
    /**
     * @var StoryStatCollection|null
     */
    private $collection;

    /**
     * @param array $contentItems list<Slide>
     */
    public function __construct(array $contentItems, StoryStatCollection $collection = null)
    {
        $this->slideIds = array_map(static function (Slide $contentItem): int {
            return $contentItem->getSlideId();
        }, $contentItems);
        $this->collection = $collection;
    }

    public function fetch(int $studentId, DateTimeInterface $date = null): int
    {
        $query = (new Query())
            ->select(['id' => new Expression('DISTINCT t.slide_id')])
            ->from(['t' => 'story_student_stat'])
            ->where(['in', 't.slide_id', $this->slideIds])
            ->andWhere(['t.student_id' => $studentId]);

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
                'id' => 't.slide_id',
                'date' => new Expression('MIN(t.created_at)'),
            ])
            ->from(['t' => 'story_student_stat'])
            ->where(['in', 't.slide_id', $this->slideIds])
            ->andWhere(['t.student_id' => $studentId])
            ->groupBy('t.slide_id');

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
