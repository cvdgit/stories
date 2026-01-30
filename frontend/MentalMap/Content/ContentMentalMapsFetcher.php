<?php

declare(strict_types=1);

namespace frontend\MentalMap\Content;

use common\components\MentalMapThreshold;
use frontend\MentalMap\history\MentalMapTreeHistoryFetcher;
use frontend\MentalMap\MentalMap;
use frontend\MentalMap\MentalMapStorySlide;
use Yii;
use yii\db\Query;

class ContentMentalMapsFetcher
{
    /**
     * @param array<array-key, int> $slideIds
     */
    public function fetch(array $slideIds, int $userId, $canEdit = false): array
    {
        if (count($slideIds) === 0) {
            return [];
        }

        $slideMentalMaps = (new Query())
            ->select([
                'slideId' => 't.slide_id',
                'mentalMapId' => 't.mental_map_id',
                'mentalMapName' => 't2.name',
                'mapRequired' => 't.required',
            ])
            ->from(['t' => MentalMapStorySlide::tableName()])
            ->innerJoin(['t2' => MentalMap::tableName()], 't.mental_map_id = t2.uuid')
            ->where(['in', 't.slide_id', $slideIds])
            ->all();

        $contentMentalMaps = [];
        foreach ($slideMentalMaps as $row) {
            $slideId = (int) $row['slideId'];

            if (!isset($contentMentalMaps[$slideId])) {
                $contentMentalMaps[$slideId] = [
                    'slideId' => $slideId,
                    'mentalMaps' => [],
                ];
            }

            $mentalMap = MentalMap::findOne($row['mentalMapId']);
            if ($mentalMap === null) {
                continue;
            }

            $threshold = MentalMapThreshold::getThreshold(Yii::$app->params, $mentalMap->payload);
            $history = (new MentalMapTreeHistoryFetcher())->fetch(
                $mentalMap->uuid,
                $userId,
                $mentalMap->getTreeData(),
                $threshold,
            );

            $contentMentalMaps[$slideId]['mentalMaps'][] = [
                'id' => $mentalMap->uuid,
                'name' => $mentalMap->name,
                'userProgress' => round(
                    count(array_filter($history, static function (array $item): bool {
                        return $item['done'];
                    })) * 100 / count($history),
                    0,
                    PHP_ROUND_HALF_UP,
                ),
                'type' => $mentalMap->map_type,
                'edit' => $canEdit ? [
                    'url' => Yii::$app->urlManagerBackend->createAbsoluteUrl(
                        ['/mental-map/editor', 'id' => $mentalMap->uuid],
                    ),
                ] : false,
                'required' => $row['mapRequired'] === '1',
            ];
        }

        return array_values($contentMentalMaps);
    }
}
