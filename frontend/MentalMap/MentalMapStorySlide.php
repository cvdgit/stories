<?php

declare(strict_types=1);

namespace frontend\MentalMap;

use yii\db\ActiveRecord;

/**
 * @property string $mental_map_id
 * @property int $slide_id
 * @property string $block_id
 * @property int $required
 */
class MentalMapStorySlide extends ActiveRecord
{
    /**
     * @param array<array-key, int> $slideIds
     * @return MentalMapStorySlide[]
     */
    public static function findAllBySlideIds(array $slideIds, bool $required): array
    {
        if (count($slideIds) === 0) {
            return [];
        }
        $query = self::find()
            ->where(['in', 'slide_id', $slideIds]);
        if ($required) {
            $query->andWhere(['required' => 1]);
        }
        return $query->all();
    }

    public function getRequired(): bool
    {
        return (int) $this->required === 1;
    }
}
