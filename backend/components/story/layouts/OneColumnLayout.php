<?php


namespace backend\components\story\layouts;


use backend\components\story\AbstractLayout;
use backend\components\story\TextBlock;

class OneColumnLayout extends AbstractLayout
{
    public function __construct()
    {
        $block = new TextBlock();
        $block->setWidth('1200px');
        $block->setHeight('auto');
        $block->setLeft('14px');
        $block->setTop('294px');
        $block->setFontSize('3em');
        $this->addBlock($block);
    }
}