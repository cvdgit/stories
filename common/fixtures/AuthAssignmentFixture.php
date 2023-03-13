<?php

declare(strict_types=1);

namespace common\fixtures;

use yii\test\ActiveFixture;

class AuthAssignmentFixture extends ActiveFixture
{
    public $tableName = 'auth_assignment';
    public $dataFile = __DIR__ . '/data/auth_assignment.php';
}
