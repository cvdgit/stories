<?php

declare(strict_types=1);

namespace modules\edu\StoryContent;

use DateTimeInterface;
use modules\edu\query\GetStoryTests\Slide;
use modules\edu\query\GetStoryTests\SlideMentalMap;
use modules\edu\query\GetStoryTests\SlideRetelling;
use modules\edu\query\GetStoryTests\SlideTest;
use modules\edu\query\GetStoryTests\StoryStatCollection;
use modules\edu\query\GetStoryTests\StoryTestsFetcher;
use modules\edu\StoryContent\fetchers\MentalMapFragmentsFetcher;
use modules\edu\StoryContent\fetchers\RetellingFetcher;
use modules\edu\StoryContent\fetchers\SlidesFetcher;
use modules\edu\StoryContent\fetchers\TestQuestionsFetcher;
use modules\edu\StoryContent\parsers\SlideMentalMapParser;
use modules\edu\StoryContent\parsers\SlideParser;
use modules\edu\StoryContent\parsers\SlideRetellingParser;
use modules\edu\StoryContent\parsers\SlideTestParser;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

class StoryContentService
{
    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function getStoryTotalContentItems(int $storyId): int
    {
        $slideContent = (new StoryTestsFetcher())->fetchWithSpeechTrainerMentalMaps($storyId);

        $contentParserMap = [
            Slide::class => SlideParser::class,
            SlideMentalMap::class => SlideMentalMapParser::class,
            SlideRetelling::class => SlideRetellingParser::class,
            SlideTest::class => SlideTestParser::class,
        ];

        $count = 0;
        foreach ($slideContent as $type => $contentItem) {
            $parserClassName = $contentParserMap[$type] ?? null;
            if ($parserClassName) {
                $count += (new $parserClassName($contentItem))->parse();
            }
        }
        return $count;
    }

    private $contentFetcherMap = [
        Slide::class => SlidesFetcher::class,
        SlideTest::class => TestQuestionsFetcher::class,
        SlideMentalMap::class => MentalMapFragmentsFetcher::class,
        SlideRetelling::class => RetellingFetcher::class,
    ];

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function getStudentFactContentItemsCount(int $studentId, int $storyId): int
    {
        $slideContent = (new StoryTestsFetcher())->fetchWithSpeechTrainerMentalMaps($storyId);

        $count = 0;
        foreach ($this->contentFetcherMap as $slideContentItemClassName => $fetcherClassName) {
            $count += (new $fetcherClassName(
                $slideContent->find($slideContentItemClassName),
            ))->fetch($studentId);
        }
        return $count;
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function getStudentFactContentItemsDetail(int $studentId, int $storyId): StoryStatCollection
    {
        $slideContent = (new StoryTestsFetcher())->fetchWithSpeechTrainerMentalMaps($storyId);

        $collection = new StoryStatCollection();
        foreach ($this->contentFetcherMap as $slideContentItemClassName => $fetcherClassName) {
            $rows = (new $fetcherClassName(
                $slideContent->find($slideContentItemClassName),
            ))->fetchRows($studentId);
            $collection[$slideContentItemClassName] = $rows;
        }

        return $collection;
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function getStudentFactContentItemsCountByDate(
        StoryStatCollection $collection,
        int $studentId,
        int $storyId,
        DateTimeInterface $date
    ): int {
        $slideContent = (new StoryTestsFetcher())->fetchWithSpeechTrainerMentalMaps($storyId);

        $count = 0;
        foreach ($this->contentFetcherMap as $slideContentItemClassName => $fetcherClassName) {
            $count += (new $fetcherClassName(
                $slideContent->find($slideContentItemClassName),
                $collection,
            ))->fetch($studentId, $date);
        }

        return $count;
    }
}
