<?php

namespace backend\components\story\reader;

use backend\components\story\AbstractBlock;
use backend\components\story\reader\blocks\ImageReader;
use backend\components\story\reader\blocks\VideoReader;

class HtmlBlockReader implements ReaderInterface
{

    private $pqBlock;

    private $blockMap = [
        AbstractBlock::TYPE_IMAGE => ImageReader::class,
        AbstractBlock::TYPE_VIDEO => VideoReader::class,
        AbstractBlock::TYPE_VIDEOFILE => VideoReader::class,
    ];

    public function __construct(string $html)
    {
        $this->pqBlock = \phpQuery::newDocumentHTML($html)->find('.sl-block');
    }

    public function load(): AbstractBlock
    {
        $blockType = $this->pqBlock->attr('data-block-type');
        return $this->createReader($blockType);
    }

    private function createReader(string $type)
    {
        return (new $this->blockMap[$type]($this->pqBlock))->createBlock();
    }
}
