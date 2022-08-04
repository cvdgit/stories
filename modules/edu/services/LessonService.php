<?php

namespace modules\edu\services;

use common\components\ModelDomainException;
use modules\edu\forms\admin\SelectStoryForm;
use modules\edu\models\EduLesson;

class LessonService
{

    public function addStory(EduLesson $lessonModel, SelectStoryForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $lessonModel->addStory($form->story_id);
        if (!$lessonModel->save()) {
            throw ModelDomainException::create($lessonModel);
        }
    }
}
