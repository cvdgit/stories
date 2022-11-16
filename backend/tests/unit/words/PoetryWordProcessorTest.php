<?php

declare(strict_types=1);

namespace backend\tests\unit\words;

use backend\components\import\DefaultWordProcessor;
use backend\components\import\PoetryWordProcessor;
use backend\components\import\WordListAdapter;
use backend\tests\UnitTester;
use Codeception\Test\Unit;
use common\models\TestWord;

class PoetryWordProcessorTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testEmptyWordsException()
    {
        $words = [
            1 => $word = new TestWord(['id' => 1, 'name' => '1', 'word_list_id' => 1, 'order' => 1]),
            2 => $correct = new TestWord(['id' => 2, 'name' => '2', 'word_list_id' => 1, 'order' => 2]),
            3 => new TestWord(['id' => 3, 'name' => '3', 'word_list_id' => 1, 'order' => 3]),
            4 => new TestWord(['id' => 4, 'name' => '4', 'word_list_id' => 1, 'order' => 4]),
            5 => new TestWord(['id' => 5, 'name' => '5', 'word_list_id' => 1, 'order' => 5]),
            6 => new TestWord(['id' => 6, 'name' => '6', 'word_list_id' => 1, 'order' => 6]),
        ];
        $wordProcessor = new PoetryWordProcessor($words);
        $dto = $wordProcessor->process($word);

        $this->assertEquals($word->name, $dto->getName());
        $this->assertEquals(5, $dto->getAnswersCount());
        $this->assertEquals($correct->name, $dto->getCorrectAnswers()[0]->getName());
    }
}
