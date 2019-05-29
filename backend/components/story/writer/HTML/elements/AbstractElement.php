<?php


namespace backend\components\story\writer\HTML\elements;


abstract class AbstractElement
{
    /** @var string */
    protected $tagName;
    protected $attributes = [];

    /**
     * @return string
     */
    public function getTagName(): string
    {
        return $this->tagName;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}