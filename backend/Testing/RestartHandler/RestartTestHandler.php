<?php

declare(strict_types=1);

namespace backend\Testing\RestartHandler;

use frontend\events\RestartTestEvent;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\db\Exception;

final class RestartTestHandler
{
    /**
     * @throws Exception
     */
    public function handle(RestartTestEvent $event): void
    {
        $command = Yii::$app->db->createCommand();
        $command->insert(
            'test_restart_log',
            [
                'id' => Uuid::uuid4()->toString(),
                'test_id' => $event->getTestId(),
                'student_id' => $event->getStudentId(),
                'created_at' => time(),
            ],
        );
        $command->execute();
    }
}
