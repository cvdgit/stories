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
            'id' => $this->question->getId(),
            'name' => $this->question->getName(),
            'image' => $this->question->getImage(),
            'orig_image' => $this->question->getOrigImage(),
            'original_image' => true,
            'correct_number' => $this->question->getCorrectAnswerNumber(),
            'storyTestAnswers' => $this->createAnswers($this->question->getAnswers()),
            'lastAnswerIsCorrect' => $this->question->isLastAnswerIsCorrect(),
        ];
    }

}