<?php

declare(strict_types=1);

namespace common\fixtures;

use common\models\Category;
use yii\test\ActiveFixture;

class CategoryFixture extends ActiveFixture
{
    public $modelClass = Category::class;
    public $dataFile = __DIR__ . '/data/category.php';
}
