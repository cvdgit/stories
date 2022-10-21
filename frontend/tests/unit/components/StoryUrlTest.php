<?php namespace frontend\tests\helpers;

use frontend\components\EduStoryUrlMatcher;

class StoryUrlTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;

    public function testTest()
    {
        $sut = new EduStoryUrlMatcher();
        $result = $sut->match('https://wikids.test:8443/edu/story/2?program_id=1');
        $this->assertEquals(['field' => 'id', 'value' => '2'], $result);
    }
}
