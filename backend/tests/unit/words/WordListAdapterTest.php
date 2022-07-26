<?php
namespace backend\tests\words;

use backend\components\import\DefaultWordProcessor;
use backend\components\import\WordListAdapter;

class WordListAdapterTest extends \Codeception\Test\Unit
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
    public function testEmptyWordsException()
    {
        $this->expectExceptionMessage('Список слов пуст');
        $adapter = new WordListAdapter([], new DefaultWordProcessor([], 0));
    }
}
