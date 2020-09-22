<?php


namespace backend\components\training\base;


class BaseQuestionSerializer
{

    private $question;

    public function __construct(BaseQuestion $question)
    {
        $this->question = $question;
    }

    private function createAnswers($answers): array
    {
        return array_map(static function(Answer $item) {
            return (new AnswerSerializer($item))->serialize();
        }, $answers);
    }

    public function serialize(): array
    {
        return [
            'test_id' => $this->question->getTestID(),
            'id' => $this->question->getId(),
            'name' => $this->question->getName(),
            'image' => $this->question->getImage(),
            'correct_number' => $this->question->getCorrectAnswerNumber(),
            'storyTestAnswers' => $this->createAnswers($this->question->getAnswers()),
            'lastAnswerIsCorrect' => $this->question->isLastAnswerIsCorrect(),
        ];
    }

}