<?php


namespace backend\components\story;


class TextBlock extends AbstractBlock
{

    protected $type = AbstractBlock::TYPE_TEXT;

    protected $text;
    protected $fontSize;

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }

    /**
     * @param mixed $fontSize
     */
    public function setFontSize($fontSize): void
    {
        $this->fontSize = $fontSize;
    }

}