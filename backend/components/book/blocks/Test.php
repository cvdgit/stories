<?php

namespace backend\components\book\blocks;

class Test extends AbstractTest
{

    public $header;
    public $description;

    protected $testID;

    public function __construct($testID)
    {
        $this->testID = $testID;
        parent::__construct();
    }

    public function getTestID()
    {
        return $this->testID;
    }

}