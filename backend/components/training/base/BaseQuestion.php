<?php

namespace backend\components\training\base;

class BaseQuestion
{

    /** @var int */
    private $testID;

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var mixed|null */
    private $image;

    /** @var bool */
    private $lastAnswerIsCorrect;

    /** @var Answer[] */
    private $answers = [];

    public function __construct(int $testID, int $id, string $name, bool $lastAnswerIsCorrect, $image = null)
    {
        $this->testID = $testID;
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->lastAnswerIsCorrect = $lastAnswerIsCorrect;
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
     * @return Answer[]
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer)
    {
        $this->answers[] = $answer;
    }

    public function getCorrectAnswerNumber()
    {
        return array_reduce($this->answers, function($carry, $item) {
            $carry += $item->isCorrect() ? 1 : 0;
            return $carry;
        });
    }

    /**
     * @return bool
     */
    public function isLastAnswerIsCorrect(): bool
    {
        return $this->lastAnswerIsCorrect;
    }

    public function serialize()
    {
        return (new BaseQuestionSerializer($this))->serialize();
    }

    /**
     * @return int
     */
    public function getTestID(): int
    {
        return $this->testID;
    }

}