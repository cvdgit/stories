<?php

declare(strict_types=1);

namespace modules\edu\fixtures;

use modules\edu\models\EduLessonStory;
use yii\test\ActiveFixture;

class EduLessonStoryFixture extends ActiveFixture
{
    public $modelClass = EduLessonStory::class;
    public $dataFile = __DIR__ . '/data/edu_lesson_story.php';
    public $depends = [
        EduLessonFixture::class,
    ];
}
