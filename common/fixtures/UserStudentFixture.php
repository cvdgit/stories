<?php

declare(strict_types=1);

namespace common\fixtures;

use common\models\UserStudent;
use yii\test\ActiveFixture;

class UserStudentFixture extends ActiveFixture
{
    public $modelClass = UserStudent::class;
    public $dataFile = __DIR__ . '/data/user_student.php';
    public $depends = [
        UserFixture::class,
    ];
}
