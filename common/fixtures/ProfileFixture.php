<?php

declare(strict_types=1);

namespace common\fixtures;

use common\models\Profile;
use yii\test\ActiveFixture;

class ProfileFixture extends ActiveFixture
{
    public $modelClass = Profile::class;
    public $dataFile = __DIR__ . '/data/profile.php';
}
