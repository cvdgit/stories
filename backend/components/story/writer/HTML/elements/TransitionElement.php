<?php

namespace backend\components\story\writer\HTML\elements;

class TransitionElement extends AbstractElement
{
    protected $tagName = 'button';
    protected $attributes = [
        'class' => 'btn',
        'data-story-id' => '',
        'data-slides' => '',
    ];
}
