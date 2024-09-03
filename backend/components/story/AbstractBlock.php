<?php

namespace backend\components\story;

abstract class AbstractBlock
{
    public const TYPE_TEXT = 'text';
    public const TYPE_HEADER = 'header';
    public const TYPE_IMAGE = 'image';
    public const TYPE_BUTTON = 'button';
    public const TYPE_TRANSITION = 'transition';
    public const TYPE_TEST = 'test';
    public const TYPE_HTML = 'html';
    public const TYPE_VIDEO = 'video';
    public const TYPE_VIDEOFILE = 'videofile';
    public const TYPE_MENTAL_MAP = 'mental_map';

    protected $width;
    protected $height;
    protected $left;
    protected $top;

    /** @var string */
    private $type;

    /** @var string */
    private $id;

    private $blockAttributes = [];

    private $elementAttributes = [];

    public function __construct()
    {
        $this->id = $this->generateID();
    }

    public function generateID(): string
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
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

    public function isMentalMap(): bool
    {
        return $this->type === self::TYPE_MENTAL_MAP;
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

    public function typeIs(string $type): bool
    {
        return $this->type === $type;
    }
}
