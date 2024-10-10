<?php

declare(strict_types=1);

namespace backend\components\story;

class MentalMapBlock extends AbstractBlock
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

    public function create(): MentalMapBLock
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
        $content = MentalMapBlockContent::createFromHtml($this->content);
        return array_merge([
            'mental_map_id' => $content->getId(),
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
