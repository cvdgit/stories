<?php

namespace common\helpers;

use yii\helpers\ArrayHelper;
use common\models\Rate;

class SubscriptionHelper
{

    public static function getSubscriptionArray(): array
    {
        return ArrayHelper::map(Rate::find()->all(), 'id', 'title');
    }

}
