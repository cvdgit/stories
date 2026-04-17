<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\repo;

use DateTimeInterface;
use modules\edu\RequiredStory\RequiredStorySessionModel;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Yii;
use yii\db\Exception;
use yii\db\Query;

class RequiredStorySessionRepository
{
    public function find(UuidInterface $requiredStoryId, DateTimeInterface $date): ?RequiredStorySession
    {
        $row = (new Query())
            ->select('*')
            ->from(['t' => RequiredStorySessionModel::tableName()])
            ->where([
                'required_story_id' => $requiredStoryId->toString(),
                'date' => $this->formatDate($date),
            ])
            ->one();
        if ($row === false) {
            return null;
        }
        return new RequiredStorySession(
            $requiredStoryId,
            $row['date'],
            (int) $row['plan'],
            (int) $row['fact'],
        );
    }

    /**
     * @throws Exception
     */
    public function create(
        UuidInterface $requiredStoryId,
        DateTimeInterface $date,
        int $plan,
        int $fact = 0
    ): RequiredStorySession {
        $command = Yii::$app->db->createCommand();
        $command->insert(
            RequiredStorySessionModel::tableName(),
            [
                'required_story_id' => $requiredStoryId->toString(),
                'date' => $formattedDate = $this->formatDate($date),
                'created_at' => time(),
                'updated_at' => time(),
                'plan' => $plan,
                'fact' => $fact,
            ],
        );
        $command->execute();
        return new RequiredStorySession(
            $requiredStoryId,
            $formattedDate,
            $plan,
            $fact,
        );
    }

    /**
     * @throws Exception
     */
    public function update(RequiredStorySession $session): void
    {
        $command = Yii::$app->db->createCommand();
        $command->update(
            RequiredStorySessionModel::tableName(),
            ['fact' => $session->getFact()],
            ['required_story_id' => $session->getRequiredStoryId()->toString(), 'date' => $session->getDate()],
        );
        $command->execute();
    }

    private function formatDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }

    public function findByRequiredStoryId(UuidInterface $requiredStoryId): array
    {
        $rows = (new Query())
            ->select('*')
            ->from(['t' => RequiredStorySessionModel::tableName()])
            ->where([
                'required_story_id' => $requiredStoryId->toString(),
            ])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        return array_map(static function (array $row) {
            return new RequiredStorySession(
                Uuid::fromString($row['required_story_id']),
                $row['date'],
                (int) $row['plan'],
                (int) $row['fact'],
            );
        }, $rows);
    }

    /**
     * @param array $requiredStoryIds
     * @param DateTimeInterface $date
     * @return RequiredStorySession[]
     */
    public function findAll(array $requiredStoryIds, DateTimeInterface $date): array
    {
        $rows = (new Query())
            ->select('*')
            ->from(['t' => RequiredStorySessionModel::tableName()])
            ->where(['in', 'required_story_id', $requiredStoryIds])
            ->andWhere([
                'date' => $this->formatDate($date),
            ])
            ->all();
        return array_map(static function(array $row) {
            return new RequiredStorySession(
                Uuid::fromString($row['required_story_id']),
                $row['date'],
                (int) $row['plan'],
                (int) $row['fact'],
            );
        }, $rows);
    }
}
