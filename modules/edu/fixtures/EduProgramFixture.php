<?php

declare(strict_types=1);

namespace modules\edu\fixtures;

use modules\edu\models\EduProgram;
use yii\test\ActiveFixture;

class EduProgramFixture extends ActiveFixture
{
    public $modelClass = EduProgram::class;
    public $dataFile = __DIR__ . '/data/edu_program.php';
}
