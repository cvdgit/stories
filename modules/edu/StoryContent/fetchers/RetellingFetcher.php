<?php

declare(strict_types=1);

namespace modules\edu\StoryContent\fetchers;

use DateTimeImmutable;
use DateTimeInterface;
use DomainException;
use modules\edu\models\EduStudent;
use modules\edu\query\GetStoryTests\SlideRetelling;
use modules\edu\query\GetStoryTests\StoryStatCollection;
use yii\db\Expression;
use yii\db\Query;

class RetellingFetcher extends AbstractFetcher implements ContentFetcherInterface
{
    /** @var int[] */
    private $retellingSlideIds;
    /**
     * @var StoryStatCollection|null
     */
    private $collection;

    public function __construct(array $contentItems, StoryStatCollection $collection = null)
    {
        $this->retellingSlideIds = array_map(static function (SlideRetelling $item) {
            return $item->getSlideId();
        }, $contentItems);

        $this->collection = $collection;
    }

    public function fetch(int $studentId, DateTimeInterface $date = null): int
    {
        $student = EduStudent::findOne($studentId);
        if ($student === null) {
            throw new DomainException('Student not found');
        }

        $query = (new Query())
            ->select(['id' => new Expression('DISTINCT rh.slide_id')])
            ->from(['rh' => 'retelling_history'])
            ->where(['in', 'rh.slide_id', $this->retellingSlideIds])
            ->andWhere(['rh.user_id' => $student->user_id])
            ->andWhere('rh.overall_similarity >= 90');

        if ($date !== null) {
            [$betweenBegin, $betweenEnd] = $this->getBetweenDates($date);
            $query->andWhere(['between', new Expression('rh.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd]);

            $rows = $query->all();
            $ids = array_map(
                static function (string $id) use ($date): array {
                    return ['id' => $id, 'date' => $date->format('Y-m-d')];
                },
                array_column($rows, 'id'),
            );
            if ($this->collection !== null && count($ids) > 0) {
                return $this->collection->filterStatRows(SlideRetelling::class, $ids);
            }
        }

        $rows = $query->all();
        return count($rows);
    }

    public function fetchRows(int $studentId, DateTimeInterface $date = null): array
    {
        $student = EduStudent::findOne($studentId);
        if ($student === null) {
            throw new DomainException('Student not found');
        }

        $query = (new Query())
            ->select([
                'id' => 't.slide_id',
                'date' => new Expression('MIN(t.created_at)'),
            ])
            ->from(['t' => 'retelling_history'])
            ->where(['in', 't.slide_id', $this->retellingSlideIds])
            ->andWhere(['t.user_id' => $student->user_id])
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
