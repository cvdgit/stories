<?php

declare(strict_types=1);

namespace common\fixtures;

use common\models\SiteSection;
use yii\test\ActiveFixture;

class SiteSectionFixture extends ActiveFixture
{
    public $modelClass = SiteSection::class;
    public $dataFile = __DIR__ . '/data/site_section.php';
    public $depends = [
        CategoryFixture::class,
    ];
}
