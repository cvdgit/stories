<?php


namespace backend\components\story;


class ButtonBlock extends TextBlock
{

    /** @var string */
    protected $url;

    protected $type = AbstractBlock::TYPE_BUTTON;

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

}