<?php


namespace backend\components\story\writer\HTML\elements;


class ParagraphElement extends AbstractElement
{
    protected $tagName = 'p';
    protected $attributes = [
        'style' => 'color: #FFFFFF;font-size: 0.8em;',
    ];
}