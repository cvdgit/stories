<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\widgets\StudentRequiredStories;

use common\models\StoryStudentProgress;
use DateTimeImmutable;
use Exception;
use modules\edu\models\EduStory;
use modules\edu\RequiredStory\repo\RequiredStorySessionRepository;
use modules\edu\RequiredStory\repo\RequiredStoryStatus;
use modules\edu\RequiredStory\RequiredStoryModel;
use yii\db\Expression;
use yii\db\Query;

class RequiredStoriesFetcher
{
    /**
     * @var RequiredStorySessionRepository
     */
    private $requiredStorySessionRepository;

    public function __construct(RequiredStorySessionRepository $requiredStorySessionRepository)
    {
        $this->requiredStorySessionRepository = $requiredStorySessionRepository;
    }

    /**
     * @return WidgetRequiredStory[]
     * @throws Exception
     */
    public function fetchAllForWidget(int $studentId): array
    {
        $query = (new Query())
            ->select([
                'id' => 't.id',
                'storyId' => 't.story_id',
                'storyTitle' => 's.title',
                'storyCover' => 's.cover',
                'storyProgress' => 'p.progress',
            ])
            ->from(['t' => RequiredStoryModel::tableName()])
            ->innerJoin(['s' => EduStory::tableName()], 't.story_id = s.id')
            ->leftJoin(['p' => StoryStudentProgress::tableName()], 't.story_id = p.story_id AND p.student_id = t.student_id')
            ->where([
                't.student_id' => $studentId,
            ])
            ->andWhere(new Expression('IFNULL(p.progress, 0) < 100'))
            ->andWhere(['t.status' => (string) RequiredStoryStatus::open()])
            ->andWhere(['<=', new Expression('t.started_at + (3 * 60 * 60)'), new Expression('UNIX_TIMESTAMP()')])
            ->orderBy(['t.started_at' => SORT_ASC]);

        $rows = $query->all();

        return array_map(function (array $row): WidgetRequiredStory {
            $requiredStory = WidgetRequiredStory::fromArray($row);
            $session = $this->requiredStorySessionRepository->find(
                $requiredStory->getId(),
                new DateTimeImmutable()
            );
            if ($session !== null) {
                $requiredStory->setSession($session);
            }
            return $requiredStory;
        }, $rows);
    }
}
