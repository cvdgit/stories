<?php

namespace backend\components\book\blocks;

class HtmlTest extends AbstractTest
{

    protected $content;

    public function __construct($content)
    {
        $this->content = $content;
        parent::__construct();
    }

    public function getTestID()
    {
        $fragment = \phpQuery::newDocumentHTML($this->content);
        return $fragment->find('.new-questions')->attr('data-test-id');
    }

}