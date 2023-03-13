<?php

namespace frontend\tests\functional;

use tests\fixtures\StoryTestFixture;
use tests\fixtures\UserFixture;
use tests\fixtures\UserStudentFixture;
use common\models\User;
use frontend\tests\FunctionalTester;

class QuizRestartCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amLoggedInAs($I->grabRecord(User::class, ['username' => 'admin']));
    }

    public function getWithNoParams(FunctionalTester $I)
    {
        $I->sendAjaxRequest('GET', '/question/quiz-restart');
        $I->seeResponseCodeIs(400);
    }

    /**
     * @dataProvider paramsProvider
     */
    public function getWrong(FunctionalTester $I, \Codeception\Example $example)
    {
        $I->sendAjaxRequest('GET', '/question/quiz-restart', ['quiz_id' => $example['quizId'], 'student_id' => $example['studentId']]);
        $I->seeResponseCodeIs(400);
    }

    public function paramsProvider()
    {
        return [
            ['quizId' => null, 'studentId' => null],
            ['quizId' => 111, 'studentId' => null],
            ['quizId' => null, 'studentId' => 111],
        ];
    }

    public function successTrue(FunctionalTester $I)
    {
        $I->sendAjaxRequest('GET', '/question/quiz-restart', ['quiz_id' => 1, 'student_id' => 1]);
        $I->seeResponseCodeIs(200);
    }

    public function getOtherStudent(FunctionalTester $I)
    {
        $I->sendAjaxRequest('GET', '/question/quiz-restart', ['quiz_id' => 1, 'student_id' => 2]);
        $I->seeResponseCodeIs(403);
    }

    /**
     * Load fixtures before db transaction begin
     * Called in _before()
     * @see \Codeception\Module\Yii2::_before()
     * @see \Codeception\Module\Yii2::loadFixtures()
     * @return array
     */
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
            'student' => [
                'class' => UserStudentFixture::class,
                'dataFile' => codecept_data_dir() . 'user_student.php',
            ],
            'story_test' => [
                'class' => StoryTestFixture::class,
                'dataFile' => codecept_data_dir() . 'story_test.php',
            ],
        ];
    }
}
