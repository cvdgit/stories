<?php

declare(strict_types=1);

namespace common\fixtures;

use common\models\UserStoryHistory;
use yii\test\ActiveFixture;

class UserStoryHistoryFixture extends ActiveFixture
{
    public $modelClass = UserStoryHistory::class;
    public $dataFile = __DIR__ . '/data/user_story_history.php';
}
