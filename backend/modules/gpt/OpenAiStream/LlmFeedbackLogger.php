<?php

declare(strict_types=1);

namespace backend\modules\gpt\OpenAiStream;

use Yii;
use yii\db\Exception;
use yii\helpers\Json;

final class LlmFeedbackLogger
{
    /**
     * @throws Exception
     */
    public function save(
        string $runId,
        string $target,
        int $userId,
        array $messages,
        string $output
    ): void {
        Yii::$app->db
            ->createCommand()
            ->insert('llm_feedback', [

                'run_id' => $runId,
                'target' => $target,
                'user_id' => $userId,
                "input" => ['input' => ['messages' => $messages]],
                "output" => ['final_output' => $output],
                "created_at" => time(),
            ])
            ->execute();
    }
}
