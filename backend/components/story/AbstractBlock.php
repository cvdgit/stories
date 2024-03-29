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
    const TYPE_VIDEO = 'video';
    const TYPE_VIDEOFILE = 'videofile';

    protected $width;
    protected $height;
    protected $left;
    protected $top;

    /** @var string */
    protected $type;

    /** @var string */
    protected $id;

    private $blockAttributes = [];

    private $elementAttributes = [];

    public function __construct()
    {
        $this->id = $this->generateID();
    }

    public function generateID()
    {
        return bin2hex(random_bytes(10));
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

    public function delete(): void {}

    public function isVideo(): bool
    {
        return $this->type === self::TYPE_VIDEO;
    }

    public function isButton(): bool
    {
        return $this->type === self::TYPE_BUTTON;
    }

    public function isTest(): bool
    {
        return $this->type === self::TYPE_TEST;
    }

    public function isHtmlTest(): bool
    {
        return $this->type === self::TYPE_HTML;
    }

    public function isImage(): bool
    {
        return $this->type === self::TYPE_IMAGE;
    }

    public function setBlockAttribute(string $name, $value): void
    {
        $this->blockAttributes[$name] = $value;
    }

    public function getBlockAttribute(string $name)
    {
        return $this->blockAttributes[$name];
    }

    public function getBlockAttributes(): array
    {
        return $this->blockAttributes;
    }

    public function setBlockAttributes(array $attrs): void
    {
        $this->blockAttributes = $attrs;
    }

    public function setElementAttributes(array $attrs): void
    {
        $this->elementAttributes = $attrs;
    }

    public function setElementAttribute(string $name, $value): void
    {
        $this->elementAttributes[$name] = $value;
    }

    public function getElementAttribute(string $name)
    {
        return $this->elementAttributes[$name] ?? null;
    }

    public function getElementAttributes(): array
    {
        return $this->elementAttributes;
    }
}