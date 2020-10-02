<?php

namespace backend\components;

use backend\components\training\base\Answer;
use backend\components\training\base\QuestionCollection;
use backend\components\training\local\WordQuestion;

class NumPadBuilder
{

    private $testID;
    private $data;
    private $stars;

    public function __construct(int $testID, $data, $stars)
    {
        $this->testID = $testID;
        $this->data = $data;
        $this->stars = $stars;
    }

    public function build(QuestionCollection $collection)
    {
        foreach ($this->data as $word) {

            $correctAnswer = (int)$word['correct_answer'];

            $stars = 0;
            foreach ($this->stars as $star) {
                if ((int)$star['entity_id'] === (int)$word['id'] && (int)$star['answer_entity_id'] === $correctAnswer) {
                    $stars = $star['stars'];
                    break;
                }
            }

            $question = new WordQuestion($this->testID, $word['id'], $word['name'], true,0, 0, $stars);
            $question->addAnswer(new Answer(1, $correctAnswer, true));

            $collection->addQuestion($question);
        }
    }
}