<?php

namespace backend\components\story\writer\HTML\elements;

class ButtonElement extends AbstractElement
{

    protected $tagName = 'a';
    protected $attributes = [
        'href' => '#',
        'target' => '_blank',
        'class' => 'btn',
    ];
}
