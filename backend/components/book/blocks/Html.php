<?php

namespace backend\components\book\blocks;

use common\models\StoryTest;

class Html extends Block
{

    public $header;
    public $description;

    private $content;

    public function __construct($content)
    {
        $this->content = $content;
        $this->generate();
    }

    private function generate()
    {
        $fragment = \phpQuery::newDocumentHTML($this->content);
        $testId = $fragment->find('.new-questions')->attr('data-test-id');

        $test = StoryTest::findModel($testId);
        $this->header = $test->header;
        $this->description = $test->description_text;
    }

}