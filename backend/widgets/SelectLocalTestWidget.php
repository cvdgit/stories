<?php

namespace backend\widgets;

use common\models\StoryTest;

class SelectLocalTestWidget extends SelectTestWidget
{

    protected function getData()
    {
        return StoryTest::getLocalTestArray();
    }

}