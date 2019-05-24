<?php


namespace backend\components\story\layouts;


use backend\components\story\AbstractLayout;
use backend\components\story\ImageBlock;
use backend\components\story\TextBlock;

class TwoColumnLayout extends AbstractLayout
{
    public function __construct()
    {

        $block = new ImageBlock();
        $block->setWidth('973px');
        $block->setHeight('720px');
        $block->setLeft(0);
        $block->setTop(0);
        $this->addBlock($block);

        $block = new TextBlock();
        $block->setWidth('290px');
        $block->setHeight('auto');
        $block->setLeft('983px');
        $block->setTop('9px');
        $block->setFontSize('0.8em');
        $this->addBlock($block);
    }
}