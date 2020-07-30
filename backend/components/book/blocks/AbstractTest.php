<?php

namespace backend\components\book\blocks;

use common\models\StoryTest;

abstract class AbstractTest extends Block
{

    public function __construct()
    {
        $this->generate();
    }

    abstract public function getTestID();

    public function generate()
    {
        $test = StoryTest::findModel($this->getTestID());
        $this->header = $test->header;
        $this->description = $test->description_text;
    }

}