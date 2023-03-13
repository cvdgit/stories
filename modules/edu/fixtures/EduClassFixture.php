<?php

declare(strict_types=1);

namespace modules\edu\fixtures;

use modules\edu\models\EduClass;
use yii\test\ActiveFixture;

class EduClassFixture extends ActiveFixture
{
    public $modelClass = EduClass::class;
    public $dataFile = __DIR__ . '/data/edu_class.php';
}
