<?php

declare(strict_types=1);

namespace frontend\TableOfContents;

use common\components\MentalMapThreshold;
use common\models\StudentQuestionProgress;
use frontend\MentalMap\history\MentalMapHistoryFetcher;
use frontend\MentalMap\history\MentalMapTreeHistoryFetcher;
use frontend\MentalMap\MentalMap;
use frontend\Retelling\Retelling;
use frontend\SpeechTrainer\SpeechTrainerContentsFetcher;
use modules\edu\components\ArrayHelper;
use modules\edu\query\GetStoryTests\Slide;
use modules\edu\query\GetStoryTests\SlideMentalMap;
use modules\edu\query\GetStoryTests\SlideRetelling;
use modules\edu\query\GetStoryTests\SlideTest;
use modules\edu\query\GetStoryTests\StoryTestsFetcher;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Expression;
use yii\db\Query;
use yii\web\NotFoundHttpException;

class TableOfContentsStatusFetcher
{
    /**
     * @var SpeechTrainerContentsFetcher
     */
    private $speechTrainerContentsFetcher;

    public function __construct(SpeechTrainerContentsFetcher $speechTrainerContentsFetcher)
    {
        $this->speechTrainerContentsFetcher = $speechTrainerContentsFetcher;
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function fetch(int $storyId, int $userId, int $studentId): array
    {
        $slideContent = (new StoryTestsFetcher())->fetch($storyId);

        $slides = $slideContent->find(Slide::class);

        $contents = $this->speechTrainerContentsFetcher->fetch(
            array_map(static function (Slide $slideItem): int {
                return $slideItem->getSlideId();
            }, $slides),
            $userId,
        );

        $history = [];
        foreach ($slides as $slide) {
            $contentRow = ArrayHelper::array_find(
                $contents,
                static function(array $content) use ($slide): bool {
                    return $content['slideId'] === $slide->getSlideId();
                }
            );
            $progress = 0;
            if ($contentRow) {
                $progress = round(
                    count(array_filter($contentRow['mentalMaps'], static function (array $item): bool {
                        return (int) $item['userProgress'] === 100;
                    })) * 100 / count($contentRow['mentalMaps']),
                    0,
                    PHP_ROUND_HALF_UP,
                );
            } else {
                $progress = $this->slideViewProgress(
                    $storyId,
                    $slide->getSlideId(),
                    $userId,
                );
            }
            $history[] = [
                'type' => 'slide',
                'slideId' => $slide->getSlideId(),
                'progress' => $progress,
            ];
        }

        $mentalMaps = $slideContent->find(SlideMentalMap::class);
        foreach ($mentalMaps as $mentalMapSlide) {
            $mentalMap = MentalMap::findOne($mentalMapSlide->getMentalMapId());
            if ($mentalMap === null) {
                continue;
            }

            $threshold = MentalMapThreshold::getThreshold(Yii::$app->params, $mentalMap->payload);
            if ($mentalMap->isMentalMapAsTree()) {
                $mapHistory = (new MentalMapTreeHistoryFetcher())->fetch(
                    $mentalMap->uuid,
                    $userId,
                    $mentalMap->getTreeData(),
                    $threshold,
                );
            } else {
                $mapHistory = (new MentalMapHistoryFetcher())->fetch(
                    $mentalMap->getImages(),
                    $mentalMap->uuid,
                    $userId,
                    $threshold,
                );
            }
            $progress = MentalMap::calcHistoryPercent($mapHistory, $threshold);
            $history[] = [
                'type' => 'mental-map',
                'slideId' => $mentalMapSlide->getSlideId(),
                'progress' => $progress,
            ];
        }

        $retellingItems = $slideContent->find(SlideRetelling::class);
        foreach ($retellingItems as $retellingItem) {
            $retelling = Retelling::findOne($retellingItem->getRetellingId());
            if ($retelling === null) {
                continue;
            }
            $completed = (new Query())
                ->select([
                    'overallSimilarity' => new Expression('MAX(rh.overall_similarity)'),
                ])
                ->from(['rh' => 'retelling_history'])
                ->where([
                    'story_id' => $storyId,
                    'slide_id' => $retellingItem->getSlideId(),
                    'user_id' => $userId,
                ])
                ->andWhere('rh.overall_similarity >= 90')
                ->scalar();
            $progress = $completed === null ? 0 : 100;
            $history[] = [
                'type' => 'retelling',
                'slideId' => $retellingItem->getSlideId(),
                'progress' => $progress,
            ];
        }

        $tests = $slideContent->find(SlideTest::class);
        foreach ($tests as $testItem) {
            $history[] = [
                'type' => 'test',
                'slideId' => $testItem->getSlideId(),
                'progress' => StudentQuestionProgress::findProgress(
                    $testItem->getTestId(),
                    $studentId,
                ),
            ];
        }

        return $history;
    }

    private function slideViewProgress(int $storyId, int $slideId, int $userId): int
    {
        $statRows = (new Query())
            ->select('*')
            ->from(['t' => '{{%story_statistics}}'])
            ->where([
                'story_id' => $storyId,
                'slide_id' => $slideId,
                'user_id' => $userId,
            ])
            ->all();
        return count($statRows) > 0 ? 100 : 0;
    }
}
