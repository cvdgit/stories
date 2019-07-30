<?php


namespace backend\components\story;

use backend\models\editor\TextForm;

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

    public function getValues(): array
    {
        return array_merge([
            'text' => preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $this->text),
            'text_size' => $this->fontSize,
        ], parent::getValues());
    }

    /**
     * @param TextForm $form
     */
    public function update($form)
    {
        $this->setSizeAndPosition($form->width, $form->height, $form->left, $form->top);
        $this->text = nl2br($form->text);
        $this->fontSize = $form->text_size;
    }

    public function create()
    {
        $block = new self();
        $block->setWidth('290px');
        $block->setHeight('auto');
        $block->setLeft('983px');
        $block->setTop('9px');
        $block->setFontSize('0.8em');
        $block->setText('Текст');
        return $block;
    }

}