<?php


namespace backend\components\story;


class HTMLBLock extends AbstractBlock
{

    protected $type = AbstractBlock::TYPE_HTML;

    /** @var string */
    protected $content;

    public function update($form)
    {

    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function create()
    {
        $block = new self();
        $block->setWidth('1200px');
        $block->setHeight('650px');
        $block->setLeft('40px');
        $block->setTop('20px');
        return $block;
    }

}