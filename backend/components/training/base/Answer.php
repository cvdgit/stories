<?php

namespace backend\components\training\base;

class Answer
{

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var mixed|null */
    private $image;

    private $origImage;

    /** @var bool */
    private $correct;

    /** @var mixed|string */
    private $regionID;

    /** @var int|null */
    private $order;

    /** @var string */
    private $description;

    /** @var int|null */
    private $hidden;

    public function __construct(int $id, string $name, bool $correct, string $regionID = '', $image = null, $order = null, $origImage = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->correct = $correct;
        $this->regionID = $regionID;
        $this->image = $image;
        $this->order = $order;
        $this->origImage = $origImage;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed|null
     */
    public function getImage()
    {
        return $this->image;
    }

    public function getOrigImage(): string
    {
        return (string) $this->origImage;
    }

    /**
     * @return bool
     */
    public function isCorrect(): bool
    {
        return $this->correct;
    }

    /**
     * @return mixed|string
     */
    public function getRegionID()
    {
        return $this->regionID;
    }

    /**
     * @param mixed|string $regionID
     */
    public function setRegionID($regionID): void
    {
        $this->regionID = $regionID;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return int|null
     */
    public function getHidden(): ?int
    {
        return $this->hidden;
    }

    /**
     * @param int $hidden
     */
    public function setHidden(int $hidden): void
    {
        $this->hidden = $hidden;
    }

    public function isHidden(): bool
    {
        if ($this->getHidden() === null) {
            return false;
        }
        return $this->getHidden() === 1;
    }
}
