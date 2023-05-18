<?php

declare(strict_types=1);

namespace backend\Testing\ImportQuestions\Import;

class ImportCommand
{
    private $fromTestId;
    private $toTestId;
    private $questionIds;

    public function __construct(int $fromTestId, int $toTestId, array $questionIds)
    {
        $this->fromTestId = $fromTestId;
        $this->toTestId = $toTestId;
        $this->questionIds = $questionIds;
    }

    /**
     * @return int
     */
    public function getFromTestId(): int
    {
        return $this->fromTestId;
    }

    /**
     * @return int
     */
    public function getToTestId(): int
    {
        return $this->toTestId;
    }

    /**
     * @return array
     */
    public function getQuestionIds(): array
    {
        return $this->questionIds;
    }
}
