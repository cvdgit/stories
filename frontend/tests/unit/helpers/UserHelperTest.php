<?php namespace frontend\tests\helpers;

use common\helpers\UserHelper;

class UserHelperTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testFormatUsernameSuccess()
    {
        $formattedUsername = UserHelper::formatUsername('123!@#alex-АЛЕКС ');
        $this->assertEquals($formattedUsername, '123alex_aleks');
    }

    public function testFormatUsernameFailure()
    {
        $formattedUsername = UserHelper::formatUsername('alex-АЛЕКС');
        $this->assertNotEquals($formattedUsername, 'alex-АЛЕКС');
    }
}
