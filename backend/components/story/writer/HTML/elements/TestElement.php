<?php

namespace backend\components\story\writer\HTML\elements;

class TestElement extends AbstractElement
{
    protected $tagName = 'button';
    protected $attributes = [
        'class' => 'btn',
        'data-test-id' => '',
    ];
}
