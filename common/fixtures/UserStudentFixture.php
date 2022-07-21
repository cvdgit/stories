<?php

namespace common\fixtures;

use common\models\UserStudent;
use yii\test\ActiveFixture;

class UserStudentFixture extends ActiveFixture
{

    public $modelClass = UserStudent::class;
    public $depends = [
        UserFixture::class,
    ];
}
