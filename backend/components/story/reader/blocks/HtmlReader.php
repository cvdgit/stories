<?php

namespace backend\components\story\reader\blocks;

use backend\components\story\AbstractBlock;
use backend\components\story\HTMLBLock;

class HtmlReader extends AbstractBlockReader implements BlockReaderInterface
{

    public function createBlock(): AbstractBlock
    {
        $block = new HtmlBlock();
        $block->setType(AbstractBlock::TYPE_HTML);
        $this->loadBlockProperties($block, $this->pqBlock->attr('style'));
        $block->setId($this->pqBlock->attr('data-block-id'));
        $block->setContent($this->pqBlock->find('.sl-block-content:eq(0)')->html());
        return $block;
    }
}
