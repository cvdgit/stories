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
        $q = $this->question;
        return [
            'id' => $q->getId(),
            'name' => $q->getName(),
            'image' => $q->getImage(),
            'orig_image' => $q->getOrigImage(),
            'original_image' => $q->getOrigImage() !== '',
            'correct_number' => $q->getCorrectAnswerNumber(),
            'storyTestAnswers' => $this->createAnswers($q->getAnswersWithHidden()),
            'lastAnswerIsCorrect' => $q->isLastAnswerIsCorrect(),
            'haveSlides' => $q->getHaveSlides(),
            'hint' => $q->getHint(),
            'slides' => $q->getSlides(),
            'audio_file' => $q->getAudioFile(),
            'incorrect_description' => $q->getIncorrectDescription(),
        ];
    }
}
