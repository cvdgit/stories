<?php

namespace backend\components\story;

class HTMLBLock extends AbstractBlock
{
    protected $type = AbstractBlock::TYPE_HTML;

    /** @var string */
    private $content;

    private $test_id;
    private $required;

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

    public function create(): HTMLBLock
    {
        $block = new self();
        $block->setType(AbstractBlock::TYPE_HTML);
        $block->setWidth('1280px');
        $block->setHeight('720px');
        $block->setLeft('0px');
        $block->setTop('0px');
        return $block;
    }

    public function getValues(): array
    {
        $content = TestBlockContent::createFromHtml($this->content);
        return array_merge([
            'test_id' => $content->getTestID(),
            'required' => $content->getRequired(),
        ], parent::getValues());
    }

    public function getContentObject(string $className)
    {
        return $className::createFromHtml($this->getContent());
    }
}
