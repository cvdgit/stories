<?php

declare(strict_types=1);

namespace modules\edu\fixtures;

use modules\edu\models\EduClassProgram;
use yii\test\ActiveFixture;

class EduClassProgramFixture extends ActiveFixture
{
    public $modelClass = EduClassProgram::class;
    public $dataFile = __DIR__ . '/data/edu_class_program.php';
    public $depends = [
        EduClassFixture::class,
        EduProgramFixture::class,
    ];
}
