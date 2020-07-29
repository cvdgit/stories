<?php

namespace backend\components\book\blocks;

use common\models\StoryTest;

class Test extends Block
{

    public $header;
    public $description;

    private $testID;

    public function __construct($testID)
    {
        $this->testID = $testID;
        $this->generate();
    }

    public function generate()
    {
        $test = StoryTest::findModel($this->testID);
        $this->header = $test->header;
        $this->description = $test->description_text;
    }

}