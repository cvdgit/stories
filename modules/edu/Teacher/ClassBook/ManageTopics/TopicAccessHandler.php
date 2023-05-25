<?php

declare(strict_types=1);

namespace modules\edu\Teacher\ClassBook\ManageTopics;

use Yii;

class TopicAccessHandler
{
    public function handle(TopicAccessCommand $command): void
    {
        Yii::$app->db->createCommand()
            ->delete('edu_class_book_topic_access', [
                'class_book_id' => $command->getClassBookId(),
            ])
            ->execute();

        $rows = [];
        foreach ($command->getItems() as $i => $item) {
            $rows[] = [
                'class_book_id' => $command->getClassBookId(),
                'class_program_id' => $item->class_program_id,
                'topic_id' => $item->topic_id,
                'created_at' => time(),
                'ord' => $i,
            ];
        }

        Yii::$app->db->createCommand()
            ->batchInsert('edu_class_book_topic_access', ['class_book_id', 'class_program_id', 'topic_id', 'created_at', 'ord'], $rows)
            ->execute();
    }
}
