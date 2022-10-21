<?php namespace frontend\tests\helpers;

use frontend\components\ChainUrlMatcher;
use frontend\components\EduStoryUrlMatcher;
use frontend\components\StoryUrlMatcher;

class ChainUrlMatcherTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;

    public function testEduStory()
    {
        $sut = new ChainUrlMatcher(...[
            new StoryUrlMatcher(),
            new EduStoryUrlMatcher(),
        ]);
        $result = $sut->match('https://wikids.test:8443/edu/story/2?program_id=1');
        $this->assertEquals(['field' => 'id', 'value' => '2'], $result);
    }

    public function testStory()
    {
        $sut = new ChainUrlMatcher(...[
            new StoryUrlMatcher(),
            new EduStoryUrlMatcher(),
        ]);
        $result = $sut->match('https://wikids.test:8443/story/story-alias');
        $this->assertEquals(['field' => 'alias', 'value' => 'story-alias'], $result);
    }
}
