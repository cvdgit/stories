<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Step;

class StepPayload implements \JsonSerializable
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $job;
    /**
     * @var array
     */
    private $answers;
    /**
     * @var array
     */
    private $fragments;
    /**
     * @var bool
     */
    private $isAnswerOptions;
    /**
     * @var int
     */
    private $index;

    public function __construct(
        string $id,
        string $name,
        string $job,
        array $answers,
        array $fragments,
        bool $isAnswerOptions,
        int $index
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->job = $job;
        $this->answers = $answers;
        $this->fragments = $fragments;
        $this->isAnswerOptions = $isAnswerOptions;
        $this->index = $index;
    }

    public function getJob(): string
    {
        return $this->job;
    }

    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function asArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'job' => $this->job,
            'answers' => $this->answers,
            'fragments' => $this->fragments,
            'isAnswerOptions' => $this->isAnswerOptions,
            'index' => $this->index,
        ];
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            $payload['id'],
            $payload['name'],
            $payload['job'],
            $payload['answers'] ?? [],
            $payload['fragments'] ?? [],
            $payload['isAnswerOptions'] ?? false,
            $payload['index'],
        );
    }

    public function getFragments(): array
    {
        return $this->fragments;
    }

    public function isAnswerOptions(): bool
    {
        return $this->isAnswerOptions;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function jsonSerialize(): array
    {
        return $this->asArray();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStepCorrectValues(): string
    {
        if ($this->isAnswerOptions) {
            $correctAnswers = array_filter($this->getAnswers(), static function (array $answer): bool {
                return $answer['correct'];
            });
            return implode(', ', array_map(static function (array $answer): string {
                return $answer['title'];
            }, $correctAnswers));
        }

        $values = [];
        foreach ($this->getFragments() as $fragment) {
            foreach ($fragment['placeholders'] as $placeholder) {
                $values[] = $placeholder['value'];
            }
        }
        return implode(', ', $values);
    }
}
