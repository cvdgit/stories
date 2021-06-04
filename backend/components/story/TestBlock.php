<?php


namespace backend\components\story;


use backend\models\editor\TestForm;

class TestBlock extends ButtonBlock
{

    /** @var int */
    protected $testID;

    protected $type = AbstractBlock::TYPE_TEST;

    /**
     * @return int
     */
    public function getTestID()
    {
        return $this->testID;
    }

    /**
     * @param int $testID
     */
    public function setTestID($testID): void
    {
        $this->testID = $testID;
    }

    public function getValues(): array
    {
        return array_merge([
            'text' => $this->text,
            'text_size' => $this->fontSize,
            'test_id' => $this->testID,
        ], parent::getValues());
    }

    public function create()
    {
        $block = new self();
        $block->setWidth('auto');
        $block->setHeight('auto');
        $block->setTop('600px');
        $block->setLeft('990px');
        $block->setText('Тест');
        $block->setFontSize('1em');
        $block->setUrl('#');
        return $block;
    }

    /**
     * @param TestForm $form
     */
    public function update($form)
    {
        //$this->setSizeAndPosition($form->width, $form->height, $form->left, $form->top);
        $this->text = $form->text;
        $this->fontSize = $form->text_size;
        $this->testID = $form->test_id;
    }

}