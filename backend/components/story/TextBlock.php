<?php

namespace backend\components\story;

use backend\models\editor\TextForm;

class TextBlock extends AbstractBlock
{

    protected $type = AbstractBlock::TYPE_TEXT;
    protected $text;

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

    public function getValues(): array
    {
        return array_merge([
            'text' => $this->text, // preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, preg_replace('/[\r\n]*/', '', $this->text)),
        ], parent::getValues());
    }

    /**
     * @param TextForm $form
     */
    public function update($form)
    {
        $this->text = $form->text; //nl2br($form->text);
    }

    public function create()
    {
        $block = new self();
        $block->setType(AbstractBlock::TYPE_TEXT);
        $block->setWidth('290px');
        $block->setHeight('auto');
        $block->setLeft('983px');
        $block->setTop('9px');
        $block->setText('Текст');
        return $block;
    }
}
