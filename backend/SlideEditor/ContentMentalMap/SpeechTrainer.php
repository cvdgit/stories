<?php

declare(strict_types=1);

namespace backend\SlideEditor\ContentMentalMap;

use backend\components\story\AbstractBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\RetellingBlock;
use backend\components\story\RetellingBlockContent;
use backend\MentalMap\MentalMap;
use backend\MentalMap\MentalMapStorySlide;
use backend\Retelling\Retelling;
use common\models\StorySlide;
use DomainException;
use Ramsey\Uuid\UuidInterface;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property string $id
 * @property string $name
 * @property int $slide_id
 * @property string $block_id
 * @property string|null $retelling_slide_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-read array $contentRetelling
 * @property-read array $contentMentalMaps
 * @property StorySlide $slide
 * @property-read array $contents
 */
class SpeechTrainer extends ActiveRecord
{
    public const TYPE_MENTAL_MAP = 'mental-map';
    public const TYPE_MENTAL_MAP_EVEN_FRAGMENTS = 'mental-map-even-fragments';
    public const TYPE_MENTAL_MAP_ODD_FRAGMENTS = 'mental-map-odd-fragments';
    public const TYPE_MENTAL_MAP_PLAN = 'mental-map-plan';
    public const TYPE_MENTAL_MAP_PLAN_ACCUMULATION = 'mental-map-plan-accumulation';
    public const TYPE_RETELLING = 'retelling';

    public static function getAllTypes(): array
    {
        return [
            self::TYPE_MENTAL_MAP => 'Ментальная карта',
            self::TYPE_MENTAL_MAP_EVEN_FRAGMENTS => 'Ментальная карта (четные пропуски)',
            self::TYPE_MENTAL_MAP_ODD_FRAGMENTS => 'Ментальная карта (нечетные пропуски)',
            self::TYPE_MENTAL_MAP_PLAN => 'Ментальная карта (план)',
            self::TYPE_MENTAL_MAP_PLAN_ACCUMULATION => 'План с накоплением',
            self::TYPE_RETELLING => 'Пересказ',
        ];
    }

    public static function isValidType(string $type): bool
    {
        return isset(self::getAllTypes()[$type]);
    }

    /**
     * @param int $slideId
     * @return array<array-key, SpeechTrainer>
     */
    public static function findAllBySlide(int $slideId): array
    {
        return self::find()->where(['slide_id' => $slideId])->all();
    }

    public function getContents(): array
    {
        return array_merge($this->getContentMentalMaps(), $this->getContentRetelling());
    }

    public function getContentMentalMaps(): array
    {
        $slideMentalMapRows = MentalMapStorySlide::findMentalMapRows(
            $this->slide_id,
            $this->block_id,
        );
        $mentalMaps = [];
        foreach ($slideMentalMapRows as $slideMentalMapRow) {
            $mentalMap = MentalMap::findOne($slideMentalMapRow->mental_map_id);
            if ($mentalMap === null) {
                continue;
            }
            $mentalMaps[] = [
                'id' => $slideMentalMapRow->mental_map_id,
                'title' => $mentalMap->name,
                'type' => $mentalMap->map_type,
                'fragments' => $mentalMap->getTreeData(),
                'required' => $slideMentalMapRow->getRequired(),
            ];
        }
        return $mentalMaps;
    }

    public function getContentRetelling(): array
    {
        if ($this->retelling_slide_id === null) {
            return [];
        }

        $slideModel = StorySlide::findOne($this->retelling_slide_id);
        if ($slideModel === null) {
            return [];
        }

        $slide = (new HtmlSlideReader($slideModel->getSlideOrLinkData()))->load();
        /** @var RetellingBlock|null $retellingBlock */
        $retellingBlock = null;
        foreach ($slide->getBlocks() as $slideBlock) {
            if ($slideBlock->typeIs(AbstractBlock::TYPE_RETELLING)) {
                $retellingBlock = $slideBlock;
            }
        }

        if ($retellingBlock === null) {
            throw new DomainException('Retelling block not found');
        }

        $content = RetellingBlockContent::createFromHtml($retellingBlock->getContent());

        $retelling = Retelling::findOne($content->getId());
        if ($retelling === null) {
            throw new DomainException('Retelling not found');
        }

        return [
            [
                'id' => $retelling->id,
                'title' => $retelling->name,
                'type' => 'retelling',
                'required' => $content->isRequired(),
            ],
        ];
    }

    public static function create(
        UuidInterface $id,
        string $name,
        int $slideId,
        string $blockId,
        int $retellingSlideId = null
    ): self {
        $model = new self();
        $model->id = $id->toString();
        $model->name = $name;
        $model->slide_id = $slideId;
        $model->block_id = $blockId;
        $model->retelling_slide_id = $retellingSlideId;
        $model->created_at = time();
        $model->updated_at = time();
        return $model;
    }

    public function getSlide(): ActiveQuery
    {
        return $this->hasOne(StorySlide::class, ['id' => 'slide_id']);
    }

    public function setRetellingSlideId(int $slideId): void
    {
        $this->retelling_slide_id = $slideId;
    }
}
