<?php

declare(strict_types=1);

namespace backend\MentalMap;

use yii\db\ActiveRecord;

/**
 * @property string $mental_map_id
 * @property int $slide_id
 * @property string $block_id
 * @property int $required
 */
class MentalMapStorySlide extends ActiveRecord
{
    public static function findMentalMapRow(int $slideId, string $mentalMapId): ?self
    {
        /** @var MentalMapStorySlide|null $model */
        $model = self::find()->where([
            'slide_id' => $slideId,
            'mental_map_id' => $mentalMapId,
        ])->one();
        return $model;
    }

    /**
     * @param int $slideId
     * @param string $blockId
     * @return array<array-key, MentalMapStorySlide>
     */
    public static function findMentalMapRows(int $slideId, string $blockId): array
    {
        return self::find()->where([
            'slide_id' => $slideId,
            'block_id' => $blockId,
        ])->all();
    }

    public function setRequired(bool $required): void
    {
        $this->required = $required ? 1 : 0;
    }

    public function getRequired(): bool
    {
        return (int) $this->required === 1;
    }

    public static function create(string $mentalMapId, int $slideId, string $blockId, bool $required): self
    {
        $model = new self();
        $model->mental_map_id = $mentalMapId;
        $model->slide_id = $slideId;
        $model->block_id = $blockId;
        $model->setRequired($required);
        return $model;
    }
}
