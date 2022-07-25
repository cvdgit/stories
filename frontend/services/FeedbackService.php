<?php

namespace frontend\services;

use common\models\story_feedback\StoryFeedback;
use frontend\components\ModelDomainException;
use frontend\models\feedback\CreateFeedbackForm;

class FeedbackService
{

    public function create(CreateFeedbackForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $model = StoryFeedback::create($form);
        if (!$model->save()) {
            throw ModelDomainException::create($model);
        }
    }
}
