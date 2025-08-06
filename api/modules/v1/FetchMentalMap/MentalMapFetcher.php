<?php

declare(strict_types=1);

namespace api\modules\v1\FetchMentalMap;

use backend\components\story\HTMLBLock;
use backend\components\story\reader\HTMLReader;
use common\components\MentalMapThreshold;
use common\models\Story;
use DomainException;
use frontend\MentalMap\MentalMap;
use phpQuery;
use Yii;

class MentalMapFetcher
{
    public function fetch(int $storyId): array
    {
        $storyModel = Story::findOne($storyId);
        if ($storyModel === null) {
            throw new DomainException('История не найдена');
        }

        $story = (new HTMLReader($storyModel->slidesData()))->load();
        $mentalMapIds = [];
        foreach ($story->getSlides() as $slide) {
            foreach ($slide->getBlocks() as $block) {
                if ($block->isMentalMap()) {
                    /** @var $block HTMLBLock */
                    $content = $block->getContent();
                    $fragment = phpQuery::newDocumentHTML($content);
                    $mentalMapId = $fragment->find('.mental-map')->attr('data-mental-map-id');
                    $mentalMapIds[] = $mentalMapId;
                }
            }
        }

        $mentalMaps = [];

        foreach ($mentalMapIds as $mentalMapId) {
            $mentalMap = MentalMap::findOne($mentalMapId);
            if ($mentalMap === null) {
                continue;
            }
            $threshold = MentalMapThreshold::getThreshold(Yii::$app->params, $mentalMap->payload);
            if ($mentalMap->mapTypeIsMentalMapQuestions()) {
                $sourceMentalMap = MentalMap::findOne($mentalMap->source_mental_map_id);
                if ($sourceMentalMap === null) {
                    continue;
                }
                $payload = $mentalMap->payload;
                $payload['map'] = $sourceMentalMap->getMapData();
                $payload['treeView'] = $sourceMentalMap->isMentalMapAsTree();
                $payload['treeData'] = $sourceMentalMap->getTreeData();
                $payload['settings'] = $sourceMentalMap->getSettingsData();
            } else {
                $payload = $mentalMap->payload;
            }
            $payload['mapTypeIsMentalMapQuestions'] = $mentalMap->mapTypeIsMentalMapQuestions();
            $mentalMaps[] = [
                'mentalMap' => $payload,
                'threshold' => $threshold,
            ];
        }

        return $mentalMaps;
    }
}
