<?php

namespace backend\widgets;

use common\models\StoryTest;

class SelectRemoteTestWidget extends SelectTestWidget
{

    protected function getData()
    {
        return StoryTest::getRemoteTestArray();
    }
}
