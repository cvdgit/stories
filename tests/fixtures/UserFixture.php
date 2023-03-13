<?php

declare(strict_types=1);

namespace tests\fixtures;

use common\models\User;
use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = User::class;
}
