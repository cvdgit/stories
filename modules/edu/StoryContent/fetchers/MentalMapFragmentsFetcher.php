<?php

declare(strict_types=1);

namespace modules\edu\StoryContent\fetchers;

use common\components\MentalMapThreshold;
use DateTimeImmutable;
use DateTimeInterface;
use DomainException;
use modules\edu\models\EduStudent;
use modules\edu\query\GetStoryTests\SlideMentalMap;
use modules\edu\query\GetStoryTests\StoryStatCollection;
use Yii;
use yii\db\Expression;
use yii\db\Query;

class MentalMapFragmentsFetcher extends AbstractFetcher implements ContentFetcherInterface
{
    /** @var string[] */
    private $mentalMapIds;
    /**
     * @var StoryStatCollection|null
     */
    private $collection;

    public function __construct(array $contentItems, StoryStatCollection $collection = null)
    {
        $this->mentalMapIds = array_map(static function (SlideMentalMap $item): string {
            return $item->getMentalMapId();
        }, $contentItems);

        $this->collection = $collection;
    }

    public function fetch(int $studentId, DateTimeInterface $date = null): int
    {
        $student = EduStudent::findOne($studentId);
        if ($student === null) {
            throw new DomainException('Student not found');
        }

        $threshold = MentalMapThreshold::getDefaultThreshold(Yii::$app->params);
        $query = (new Query())
            ->select(['id' => new Expression('DISTINCT t.image_fragment_id')])
            ->from(['t' => 'mental_map_history'])
            ->where(['in', 't.mental_map_id', $this->mentalMapIds])
            ->andWhere([
                't.user_id' => $student->user_id,
            ])
            ->andWhere("t.overall_similarity >= IFNULL(t.threshold, $threshold)")
            ->andWhere('t.all_important_words_included IS NULL OR t.all_important_words_included = 1');

        if ($date !== null) {
            [$betweenBegin, $betweenEnd] = $this->getBetweenDates($date);
            $query->andWhere(['between', new Expression('t.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd]);

            $rows = $query->all();
            $ids = array_map(
                static function (string $id) use ($date): array {
                    return ['id' => $id, 'date' => $date->format('Y-m-d')];
                },
                array_column($rows, 'id'),
            );
            if ($this->collection !== null && count($ids) > 0) {
                return $this->collection->filterStatRows(SlideMentalMap::class, $ids);
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

        $threshold = MentalMapThreshold::getDefaultThreshold(Yii::$app->params);
        $query = (new Query())
            ->select([
                'id' => 't.image_fragment_id',
                'date' => new Expression('MIN(t.created_at)'),
            ])
            ->from(['t' => 'mental_map_history'])
            ->where(['in', 't.mental_map_id', $this->mentalMapIds])
            ->andWhere([
                't.user_id' => $student->user_id,
            ])
            ->andWhere("t.overall_similarity >= IFNULL(t.threshold, $threshold)")
            ->andWhere('t.all_important_words_included IS NULL OR t.all_important_words_included = 1')
            ->groupBy('t.image_fragment_id');

        if ($date !== null) {
            [$betweenBegin, $betweenEnd] = $this->getBetweenDates($date);
            $query->andWhere(['between', new Expression('t.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd]);
        }

        return array_map(
            static function (array $row): array {
                return [
                    'id' => $row['id'],
                    'date' => (new DateTimeImmutable('@' . $row['date']))->format('Y-m-d'),
                ];
            },
            $query->all(),
        );
    }
}
