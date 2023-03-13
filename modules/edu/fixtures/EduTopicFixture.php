<?php

declare(strict_types=1);

namespace modules\edu\fixtures;

use modules\edu\models\EduTopic;
use yii\test\ActiveFixture;

class EduTopicFixture extends ActiveFixture
{
    public $modelClass = EduTopic::class;
    public $dataFile = __DIR__ . '/data/edu_topic.php';
    public $depends = [
        EduClassProgramFixture::class,
    ];
}
