<?php
namespace backend\tests\words;

use backend\components\import\DefaultWordProcessor;
use common\models\TestWord;

class DefaultWordProcessorTest extends \Codeception\Test\Unit
{
    /**
     * @var \backend\tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testSomeFeature()
    {
        $words = [
            new TestWord([
                'id' => 1,
                'name' => '2 + 3 =',
                'correct_answer' => '5',
            ]),
            new TestWord([
                'id' => 2,
                'name' => '2 + 2 =',
                'correct_answer' => '4',
            ])
        ];
        $processor = new DefaultWordProcessor($words, 3);

        $current = new TestWord([
            'id' => 1,
            'name' => '2 + 3 =',
            'correct_answer' => '5',
        ]);
        $r = $processor->createIncorrectAnswers($current, 3);

        $this->assertCount(1, $r, 'OK');
    }
}
