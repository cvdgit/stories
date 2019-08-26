<?php

namespace backend\components\story;

abstract class AbstractBlock
{

    const TYPE_TEXT = 'text';
    const TYPE_HEADER = 'header';
    const TYPE_IMAGE = 'image';
    const TYPE_BUTTON = 'button';
    const TYPE_TRANSITION = 'transition';
    const TYPE_TEST = 'test';
    const TYPE_HTML = 'html';

    protected $width;
    protected $height;
    protected $left;
    protected $top;

    /** @var string */
    protected $type;

    /** @var string */
    protected $id;

    public function __construct()
    {
        $this->id = bin2hex(random_bytes(10));
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param mixed $width
     */
    public function setWidth($width): void
    {
        $this->width = $width;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param mixed $height
     */
    public function setHeight($height): void
    {
        $this->height = $height;
    }

    /**
     * @return mixed
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param mixed $left
     */
    public function setLeft($left): void
    {
        $this->left = $left;
    }

    /**
     * @return mixed
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * @param mixed $top
     */
    public function setTop($top): void
    {
        $this->top = $top;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function setSizeAndPosition($width, $height, $left, $top): void
    {
        $this->width = $width;
        $this->height = $height;
        $this->left = $left;
        $this->top = $top;
    }

    public function getValues(): array
    {
        return [
            'left' => $this->left,
            'top' => $this->top,
            'width' => $this->width,
            'height' => $this->height,
        ];
    }

    abstract public function update($form);

}