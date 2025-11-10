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
use yii\db\Query;

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
    public function getContents(): array
    {
        return array_merge($this->getContentMentalMaps(), $this->getContentRetelling());
    }

    public function getContentMentalMaps(): array
    {
        $slideMentalMapIds = (new Query())
            ->select('t.mental_map_id')
            ->from(['t' => MentalMapStorySlide::tableName()])
            ->where([
                't.slide_id' => $this->slide_id,
                't.block_id' => $this->block_id,
            ])
            ->all();
        $slideMentalMapIds = array_column($slideMentalMapIds, 'mental_map_id');

        $mentalMaps = [];
        foreach ($slideMentalMapIds as $slideMentalMapId) {
            $mentalMap = MentalMap::findOne($slideMentalMapId);
            if ($mentalMap === null) {
                continue;
            }
            $mentalMaps[] = [
                'id' => $slideMentalMapId,
                'title' => $mentalMap->name,
                'type' => $mentalMap->map_type,
                'fragments' => $mentalMap->getTreeData(),
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
            ],
        ];
    }

    public static function create(
        UuidInterface $id,
        string $name,
        int $slideId,
        string $blockId,
        int $retellingSlideId
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
}
