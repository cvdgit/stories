<?php


namespace backend\components\story\writer\HTML\elements;


class TestElement extends AbstractElement
{
    protected $tagName = 'button';
    protected $attributes = [
        'class' => 'btn',
        'style' => 'font-size: 1em;',
        'data-test-id' => '',
    ];
}