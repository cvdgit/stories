<?php
namespace frontend\tests\acceptance;
use frontend\tests\AcceptanceTester;
class HomeCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('Онлайн школа домашнего обучения', 'h1');
    }
}
