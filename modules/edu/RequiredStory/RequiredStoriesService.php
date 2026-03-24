<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory;

use common\models\StoryStudentProgress;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use modules\edu\query\GetStoryTests\Slide;
use modules\edu\RequiredStory\repo\RequiredStoriesRepository;
use modules\edu\RequiredStory\repo\RequiredStory;
use modules\edu\RequiredStory\repo\RequiredStoryMetadata;
use modules\edu\RequiredStory\repo\RequiredStorySession;
use modules\edu\RequiredStory\repo\RequiredStorySessionRepository;
use modules\edu\RequiredStory\widgets\StudentRequiredStories\RequiredStoriesFetcher;
use modules\edu\StoryContent\StoryContentService;
use Ramsey\Uuid\UuidInterface;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;
use yii\web\NotFoundHttpException;

class RequiredStoriesService
{
    /**
     * @var RequiredStoriesRepository
     */
    private $repo;
    /**
     * @var StoryContentService
     */
    private $storyContentService;
    /**
     * @var RequiredStorySessionRepository
     */
    private $requiredStorySessionRepository;

    public function __construct(
        RequiredStoriesRepository $repo,
        StoryContentService $storyContentService,
        RequiredStorySessionRepository $requiredStorySessionRepository
    ) {
        $this->repo = $repo;
        $this->storyContentService = $storyContentService;
        $this->requiredStorySessionRepository = $requiredStorySessionRepository;
    }

    /**
     * @throws Exception
     */
    public function fetchForStudentWidget(int $studentId): DataProviderInterface
    {
        $requiredStories = (new RequiredStoriesFetcher(
            $this->requiredStorySessionRepository,
        ))->fetchAllForWidget($studentId);
        return new ArrayDataProvider([
            'allModels' => $requiredStories,
            'pagination' => false,
        ]);
    }

    /**
     * @throws Exception
     */
    public function storyIsRequired(int $storyId, int $studentId): ?RequiredStory
    {
        return $this->repo->findRequiredStory($storyId, $studentId);
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function isAccessToOtherStories(int $studentId): bool
    {
        $activeRequiredStories = $this->repo->getActiveRequiredStories($studentId);
        if (count($activeRequiredStories) === 0) {
            return true;
        }

        $storyIds = array_map(static function (RequiredStory $requiredStory) {
            return $requiredStory->getStoryId();
        }, $activeRequiredStories);

        $progressModels = StoryStudentProgress::find()
            ->where(['student_id' => $studentId])
            ->andWhere(['in', 'story_id', $storyIds])
            ->all();
        $storyDoneProgress = array_combine(
            array_map(static function (StoryStudentProgress $progress) {
                return $progress->story_id;
            }, $progressModels),
            array_map(static function (StoryStudentProgress $progress) {
                return $progress->statusIsDone();
            }, $progressModels),
        );

        $access = true;
        foreach ($activeRequiredStories as $requiredStory) {

            $done = $storyDoneProgress[$requiredStory->getStoryId()] ?? false;
            if ($done) {
                $access = $access && $done;
                continue;
            }

            $collection = $this->storyContentService->getStudentFactContentItemsDetail(
                $studentId,
                $requiredStory->getStoryId(),
            );

            $todayFactContentItemsCount = $this->storyContentService->getStudentFactContentItemsCountByDate(
                $collection,
                $studentId,
                $requiredStory->getStoryId(),
                new DateTimeImmutable(),
            );

            $metadata = $requiredStory->getMetadata();
            $planItemCount = $metadata->getCurrentPlan($collection->getTotalItems());

            $access = $access && ($todayFactContentItemsCount >= $planItemCount);
        }
        return $access;
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function initSession(
        int $studentId,
        int $storyId,
        UuidInterface $requiredStoryId,
        RequiredStoryMetadata $metadata,
        DateTimeInterface $date
    ): RequiredStorySession {

        $session = $this->requiredStorySessionRepository->find($requiredStoryId, $date);
        if ($session === null) {

            $collection = $this->storyContentService->getStudentFactContentItemsDetail(
                $studentId,
                $storyId,
            );

            $session = $this->requiredStorySessionRepository->create(
                $requiredStoryId,
                $date,
                $metadata->getCurrentPlan($collection->getTotalItems()),
                $this->storyContentService->getStudentFactContentItemsCountByDate(
                    $collection,
                    $studentId,
                    $storyId,
                    new DateTimeImmutable(),
                ),
            );
        }
        return $session;
    }

    public function findStudentSession(UuidInterface $requiredStoryId, DateTimeInterface $date): ?RequiredStorySession
    {
        return $this->requiredStorySessionRepository->find($requiredStoryId, $date);
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function updateMetadata(int $studentId, RequiredStory $requiredStory): void
    {
        $fact = $this->storyContentService->getStudentFactContentItemsCount(
            $studentId,
            $requiredStory->getStoryId(),
        );
        $metadata = $requiredStory->getMetadata();
        $chunks = $metadata->getChunks();
        foreach ($chunks as $i => $chunk) {
            $n = (int) $chunk['n'];
            $fact -= $n;
            if ($fact <= 0) {
                $chunk['v'] = $fact + $n;
                $chunks[$i] = $chunk;
                break;
            }
            $chunk['v'] = $n;
            $chunk['done'] = true;
            $chunks[$i] = $chunk;
        }
        $metadata->setChunks($chunks);
    }
}
