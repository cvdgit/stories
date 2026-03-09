<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\repo;

use Exception;
use modules\edu\models\EduStory;
use modules\edu\models\EduStudent;
use modules\edu\RequiredStory\RequiredStoryModel;
use Ramsey\Uuid\UuidInterface;
use Yii;
use yii\db\Expression;
use yii\db\Query;

class RequiredStoriesRepository
{
    /**
     * @throws Exception
     */
    public function findById(UuidInterface $id): ?RequiredStory
    {
        $row = (new Query())
            ->select('*')
            ->from(['t' => RequiredStoryModel::tableName()])
            ->where(['id' => $id->toString()])
            ->one();
        if ($row === false) {
            return null;
        }
        return RequiredStory::fromArray($row);
    }

    /**
     * @throws Exception
     */
    public function findRequiredStory(int $storyId, int $studentId): ?RequiredStory
    {
        $row = (new Query())
            ->select('*')
            ->from(['t' => RequiredStoryModel::tableName()])
            ->where([
                'story_id' => $storyId,
                'student_id' => $studentId,
                'status' => (string) RequiredStoryStatus::open(),
            ])
            ->andWhere(['<=', 't.started_at', new Expression('UNIX_TIMESTAMP()')])
            ->one();
        if ($row === false) {
            return null;
        }
        return RequiredStory::fromArray($row);
    }

    /**
     * @return list<RequiredStory>
     * @throws Exception
     */
    public function getActiveRequiredStories(int $studentId): array
    {
        $rows = (new Query())
            ->select('*')
            ->from(['t' => RequiredStoryModel::tableName()])
            ->where([
                'student_id' => $studentId,
                'status' => (string) RequiredStoryStatus::open(),
            ])
            ->andWhere(['<=', 't.started_at', new Expression('UNIX_TIMESTAMP()')]);
        return array_map(
            static function (array $row): RequiredStory {
                return RequiredStory::fromArray($row);
            },
            $rows->all(),
        );
    }

    /**
     * @return list<RequiredStoryItem>
     * @throws Exception
     */
    public function findAll(): array
    {
        $query = (new Query())
            ->select([
                'id' => 't.id',
                'storyTitle' => 's.title',
                'studentName' => 'student.name',
                'startedAt' => 't.started_at',
                'createdAt' => 't.created_at',
                'status' => 't.status',
            ])
            ->from(['t' => RequiredStoryModel::tableName()])
            ->innerJoin(['s' => EduStory::tableName()], 't.story_id = s.id')
            ->innerJoin(['student' => EduStudent::tableName()], 't.student_id = student.id')
            ->orderBy(['t.created_at' => SORT_DESC]);
        return array_map(
            static function (array $row) {
                return RequiredStoryItem::fromArray($row);
            },
            $query->all(),
        );
    }

    /**
     * @throws \yii\db\Exception
     */
    public function update(RequiredStory $requiredStory): void
    {
        $columns = [
            'story_id' => $requiredStory->getStoryId(),
            'student_id' => $requiredStory->getStudentId(),
            'started_at' => new Expression($requiredStory->getStartedAt()->format('U') . '+ 3 * 60 * 60'),
            'days' => $requiredStory->getDays(),
            'metadata' => $requiredStory->getMetadata(),
            'status' => (string) $requiredStory->getStatus(),
        ];

        $command = Yii::$app->db->createCommand();
        $command->update(
            RequiredStoryModel::tableName(),
            $columns,
            ['id' => $requiredStory->getId()],
        );
        $command->execute();
    }

    /**
     * @throws \yii\db\Exception
     */
    public function updateStatus(UuidInterface $id, RequiredStoryStatus $status): void
    {
        $command = Yii::$app->db->createCommand();
        $command->update(
            RequiredStoryModel::tableName(),
            ['status' => (string) $status],
            ['id' => $id],
        );
        $command->execute();
    }

    /**
     * @throws \yii\db\Exception
     */
    public function create(RequiredStory $requiredStory): void
    {
        $columns = [
            'id' => $requiredStory->getId()->toString(),
            'story_id' => $requiredStory->getStoryId(),
            'student_id' => $requiredStory->getStudentId(),
            'created_at' => new Expression($requiredStory->getCreatedAt()->format('U') . '+ 3 * 60 * 60'),
            'created_by' => $requiredStory->getCreatedBy(),
            'started_at' => new Expression($requiredStory->getStartedAt()->format('U') . '+ 3 * 60 * 60'),
            'days' => $requiredStory->getDays(),
            'status' => (string) $requiredStory->getStatus(),
            'metadata' => $requiredStory->getMetadata(),
        ];
        $command = Yii::$app->db->createCommand();
        $command->insert(
            RequiredStoryModel::tableName(),
            $columns,
        );
        $command->execute();
    }

    /**
     * @throws \yii\db\Exception
     */
    public function delete(UuidInterface $id): void
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(RequiredStoryModel::tableName(), ['id' => $id->toString()]);
        $command->execute();
    }
}
