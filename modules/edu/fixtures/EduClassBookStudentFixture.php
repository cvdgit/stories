<?php

declare(strict_types=1);

namespace modules\edu\fixtures;

use modules\edu\models\EduClassBookStudent;
use yii\test\ActiveFixture;

class EduClassBookStudentFixture extends ActiveFixture
{
    public $modelClass = EduClassBookStudent::class;
    public $dataFile = __DIR__ . '/data/edu_class_book_student.php';
    public $depends = [
        EduClassBookFixture::class,
    ];
}
