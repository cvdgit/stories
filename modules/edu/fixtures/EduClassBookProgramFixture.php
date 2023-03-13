<?php

declare(strict_types=1);

namespace modules\edu\fixtures;

use modules\edu\models\EduClassBookProgram;
use yii\test\ActiveFixture;

class EduClassBookProgramFixture extends ActiveFixture
{
    public $modelClass = EduClassBookProgram::class;
    public $dataFile = __DIR__ . '/data/edu_class_book_program.php';
    public $depends = [
        EduClassBookFixture::class,
        EduClassProgramFixture::class,
    ];
}
