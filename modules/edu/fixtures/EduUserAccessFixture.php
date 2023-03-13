<?php

declare(strict_types=1);

namespace modules\edu\fixtures;

use modules\edu\models\EduUserAccess;
use yii\test\ActiveFixture;

class EduUserAccessFixture extends ActiveFixture
{
    public $modelClass = EduUserAccess::class;
    public $dataFile = __DIR__ . '/data/edu_user_access.php';
}
