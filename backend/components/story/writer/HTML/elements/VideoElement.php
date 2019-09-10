<?php


namespace backend\components\story\writer\HTML\elements;


class VideoElement extends AbstractElement
{

    protected $tagName = 'div';
    protected $attributes = [
        'data-video-id' => '',
        'data-seek-to' => '',
    ];

}