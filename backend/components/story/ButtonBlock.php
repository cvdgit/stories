<?php


namespace backend\components\story;

use backend\models\editor\ButtonForm;

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

    public function getValues(): array
    {
        return array_merge([
            'text' => $this->text,
            'text_size' => $this->fontSize,
            'url' => $this->url,
        ], parent::getValues());
    }

    public function create()
    {
        $block = new self();
        $block->setWidth('auto');
        $block->setHeight('auto');
        $block->setTop('500px');
        $block->setLeft('990px');
        $block->setText('Название');
        $block->setFontSize('1em');
        $block->setUrl('#');
        return $block;
    }

    /**
     * @param ButtonForm $form
     */
    public function update($form)
    {
        $this->setSizeAndPosition($form->width, $form->height, $form->left, $form->top);
        $this->text = $form->text;
        $this->fontSize = $form->text_size;
        $this->url = $form->url;
    }

}