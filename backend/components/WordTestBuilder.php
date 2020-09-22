<?php

namespace backend\components;

use backend\components\training\base\Answer;
use backend\components\training\base\QuestionCollection;
use backend\components\training\local\WordQuestion;

class WordTestBuilder
{

    private $testID;
    private $data;
    private $stars;
    private $collection;

    public function __construct($testID, $data, $dataCount, $stars)
    {
        $this->testID = $testID;
        $this->data = $data;
        $this->stars = $stars;
        $this->collection = new QuestionCollection($dataCount);
    }

    public function build()
    {

        foreach ($this->data as $word) {

            $stars = 0;
            foreach ($this->stars as $star) {
                if ((int)$star['entity_id'] === (int)$word['id'] && in_array((int)$star['answer_entity_id'], [1], true)) {
                    $stars = $star['stars'];
                    break;
                }
            }

            $question = new WordQuestion($this->testID, $word['id'], $word['name'], true,0, 0, $stars);
            $question->addAnswer(new Answer(1, 'Правильно', true));
            $question->addAnswer(new Answer(2, 'Неправильно', false));
            $this->collection->addQuestion($question);
        }
        return $this->collection;
    }

}