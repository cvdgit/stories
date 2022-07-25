<?php

namespace backend\services;

use common\components\ModelDomainException;
use common\models\story_feedback\StoryFeedback;
use DomainException;

class FeedbackService
{

    public function success(int $id): void
    {
        if ((!$feedbackModel = StoryFeedback::findOne($id)) === null) {
            throw new DomainException('Запись обратной связи не найдена');
        }

        $feedbackModel->setStatusDone();
        if (!$feedbackModel->save()) {
            throw ModelDomainException::create($feedbackModel);
        }
    }
}
