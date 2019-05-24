<?php


namespace backend\components\markup;


use backend\components\StoryMarkup;
use backend\components\StoryRenderableInterface;

class ButtonMarkup extends StoryMarkup implements StoryRenderableInterface
{
    protected $defaultMarkup = [
        'tagName' => 'button',
        'attributes' => [],
    ];

    public function __construct($owner, $tagName = '', $attributes = [], $content = '')
    {
        if (empty($tagName)) {
            $tagName = $this->defaultMarkup['tagName'];
        }
        if (count($attributes) === 0) {
            $attributes = $this->defaultMarkup['attributes'];
        }
        parent::__construct($owner, $tagName, $attributes, $content);
    }

    public function render(): string
    {
        return $this->getTag('KNOPKA');
    }
}