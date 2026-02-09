<?php

declare(strict_types=1);

namespace frontend\SpeechTrainer;

use common\components\MentalMapThreshold;
use frontend\MentalMap\Content\StorySlidesContentsFetcher;
use frontend\MentalMap\history\MentalMapTreeHistoryFetcher;
use frontend\MentalMap\MentalMap;
use Yii;

class SpeechTrainerContentsFetcher
{
    /**
     * @param array<array-key, int> $slideIds
     */
    public function fetch(array $slideIds, int $userId, bool $required = true): array
    {
        $contents = (new StorySlidesContentsFetcher())->fetch($slideIds, $required);
        if (count($contents) === 0) {
            return [];
        }
        $contentMentalMaps = [];
        foreach ($contents as $content) {
            $slideId = $content->getSlideId();
            if (!isset($contentMentalMaps[$slideId])) {
                $contentMentalMaps[$slideId] = [
                    'slideId' => $slideId,
                    'mentalMaps' => [],
                ];
            }

            $mentalMap = MentalMap::findOne($content->getMentalMapId()->toString());
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

            $userProgress = 0;
            if (count($history) > 0) {
                $userProgress = round(
                    count(array_filter($history, static function (array $item): bool {
                        return $item['done'];
                    })) * 100 / count($history),
                    0,
                    PHP_ROUND_HALF_UP,
                );
            }

            $contentMentalMaps[$slideId]['mentalMaps'][] = [
                'id' => $mentalMap->uuid,
                'userProgress' => $userProgress,
            ];
        }
        return $contentMentalMaps;
    }
}
