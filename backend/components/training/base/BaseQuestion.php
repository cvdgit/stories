<?php

namespace backend\components\training\base;

class BaseQuestion
{

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var mixed|null */
    private $image;

    private $origImage;

    /** @var bool */
    private $lastAnswerIsCorrect;

    /** @var Answer[] */
    private $answers = [];

    /** @var bool */
    private $haveSlides = false;

    private $hint;
    private $slides;
    private $audioFile;

    public function __construct(int $id,
                                string $name,
                                bool $lastAnswerIsCorrect,
                                $image = null,
                                $origImage = null,
                                $hint = null,
                                $audioFile = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->origImage = $origImage;
        $this->lastAnswerIsCorrect = $lastAnswerIsCorrect;
        $this->hint = $hint;
        $this->audioFile = $audioFile;
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

    protected function makeStars(array $starsData, BaseQuestion $question)
    {
        $stars = 0;
        $questionID = $question->getId();
        $correctAnswerIDs = $question->getCorrectAnswerIDs();
        foreach ($starsData as $star) {
            if ((int)$star['entity_id'] === $questionID && in_array((int)$star['answer_entity_id'], $correctAnswerIDs, true)) {
                $stars = $star['stars'];
                break;
            }
        }
        return $stars;
    }

    public function getCorrectAnswerIDs()
    {
        return array_map(function(Answer $answer) {
            return $answer->getId();
        }, $this->answers);
    }

    public function getOrigImage(): string
    {
        return (string)$this->origImage;
    }

    public function setHaveSlides(bool $value): void
    {
        $this->haveSlides = $value;
    }

    public function getHaveSlides(): bool
    {
        return $this->haveSlides;
    }

    public function setHint(string $hint): void
    {
        $this->hint = $hint;
    }

    public function getHint(): ?string
    {
        return $this->hint;
    }

    public function setSlides($slides): void
    {
        $this->slides = $slides;
    }

    public function getSlides()
    {
        return $this->slides;
    }

    public function setAudioFile($audioFile): void
    {
        $this->audioFile = $audioFile;
    }

    public function getAudioFile()
    {
        return $this->audioFile;
    }
}
