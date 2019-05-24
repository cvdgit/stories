<?php


namespace backend\components\story\writer\HTML\elements;


class ButtonElement extends AbstractElement
{

    protected $tagName = 'button';
    protected $attributes = [
        'class' => 'btn',
        'style' => 'font-size: 1em;',
    ];

}