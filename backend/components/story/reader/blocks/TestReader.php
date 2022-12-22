<?php

namespace backend\components\story\reader\blocks;

use backend\components\story\AbstractBlock;
use backend\components\story\TestBlock;

class TestReader extends AbstractBlockReader implements BlockReaderInterface
{

    public function createBlock(): AbstractBlock
    {
        $block = new TestBlock();
        $block->setType(AbstractBlock::TYPE_TEST);
        $this->loadBlockProperties($block, $this->pqBlock->attr('style'));
        $block->setId($this->pqBlock->attr('data-block-id'));
        $block->setTestID($this->pqBlock->find('button')->attr('data-test-id'));
        return $block;
    }
}
