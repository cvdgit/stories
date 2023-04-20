<?php

declare(strict_types=1);

namespace common\Tags;

class CreateTagsHandler
{
    public function handle(CreateTagsCommand $command): int
    {
        \Yii::$app->db->createCommand()
            ->insert('tag', [
                'name' => $command->getTag(),
                'frequency' => 1,
            ])
            ->execute();
        return (int) \Yii::$app->db->lastInsertID;
    }
}
