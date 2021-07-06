<?php

namespace backend\components\story\writer\HTML;

class TextBlockMarkup extends AbstractMarkup
{

    protected function getBlockAttributes(): array
    {
        return [
            'class' => 'sl-block',
            'data-block-id' => $this->block->getId(),
            'data-block-type' => 'text',
        ];
    }

    protected function getContentBlockAttributes(): array
    {
        return [
            'class' => 'sl-block-content',
            'data-placeholder-tag' => $this->element->getTagName(),
            'data-placeholder-text' => 'Text',
        ];
    }

}