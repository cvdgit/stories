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

    /** @var bool */
    private $correct;

    /** @var mixed|string */
    private $regionID;

    public function __construct(int $id, string $name, bool $correct, $regionID = '', $image = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->correct = $correct;
        $this->regionID = $regionID;
        $this->image = $image;
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

}