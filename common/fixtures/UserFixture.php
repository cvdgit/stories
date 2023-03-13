<?php

declare(strict_types=1);

namespace common\fixtures;

use common\models\User;
use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = User::class;
    public $dataFile = __DIR__ . '/data/user.php';
}
