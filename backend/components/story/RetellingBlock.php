<?php

declare(strict_types=1);

namespace backend\components\story;

class RetellingBlock extends AbstractBlock
{
    private $type = AbstractBlock::TYPE_HTML;

    /** @var string */
    private $content;

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

    public function create(): RetellingBlock
    {
        $block = new self();
        $block->setWidth('1280px');
        $block->setHeight('720px');
        $block->setLeft('0px');
        $block->setTop('0px');
        return $block;
    }

    public function getValues(): array
    {
        $content = RetellingBlockContent::createFromHtml($this->content);
        return array_merge([
            'retelling_id' => $content->getId(),
            'required' => $content->isRequired() ? '1' : '0',
        ], parent::getValues());
    }

    public function getContentObject(string $className): string
    {
        return '';
    }

    public static function fromBlock(): self
    {
        return new self();
    }
}
