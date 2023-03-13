<?php

declare(strict_types=1);

namespace modules\edu\fixtures;

use modules\edu\models\EduLesson;
use yii\test\ActiveFixture;

class EduLessonFixture extends ActiveFixture
{
    public $modelClass = EduLesson::class;
    public $dataFile = __DIR__ . '/data/edu_lesson.php';
    public $depends = [
        EduTopicFixture::class,
    ];
}
