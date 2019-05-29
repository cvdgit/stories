<?php


namespace backend\components\story\writer\HTML\elements;



class HeaderElement extends AbstractElement
{

    protected $tagName = 'h1';
    protected $attributes = [
        'style' => 'font-size: 3em;',
    ];

}