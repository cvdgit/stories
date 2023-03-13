<?php

declare(strict_types=1);

namespace modules\edu\fixtures;

use modules\edu\models\StudentLogin;
use yii\test\ActiveFixture;

class StudentLoginFixture extends ActiveFixture
{
    public $modelClass = StudentLogin::class;
    public $dataFile = __DIR__ . '/data/student_login.php';
}
