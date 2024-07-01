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
            'test_id' => $this->testID,
        ], parent::getValues());
    }

    public function create(): TestBlock
    {
        $block = new self();
        $block->setType(AbstractBlock::TYPE_TEST);
        $block->setWidth('auto');
        $block->setHeight('auto');
        $block->setTop('600px');
        $block->setLeft('990px');
        $block->setText('Тест');
        $block->setUrl('#');
        return $block;
    }

    /**
     * @param TestForm $form
     */
    public function update($form)
    {
        $this->text = $form->text;
        $this->testID = $form->test_id;
    }
}
