<?php

declare(strict_types=1);

namespace frontend\services;

use common\models\StudentQuestionProgress;
use frontend\components\ModelDomainException;

class QuestionProgressService
{
    public function saveProgress(int $studentId, int $testId, int $progress, $topicId = null): void
    {
        if (($testingProgress = StudentQuestionProgress::findProgressModel($studentId, $testId)) === null) {
            $testingProgress = StudentQuestionProgress::create($studentId, (int)$topicId, $progress, $testId);
        }

        $testingProgress->updateProgress($progress);

        if (!$testingProgress->save()) {
            throw ModelDomainException::create($testingProgress);
        }
    }
}
