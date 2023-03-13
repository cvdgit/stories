<?php

declare(strict_types=1);

namespace modules\edu\fixtures;

use modules\edu\models\EduClassBook;
use yii\test\ActiveFixture;

class EduClassBookFixture extends ActiveFixture
{
    public $modelClass = EduClassBook::class;
    public $dataFile = __DIR__ . '/data/edu_class_book.php';
    public $depends = [
        EduClassFixture::class,
    ];
}
