<?php

namespace backend\components\training\collection;

use backend\components\training\base\QuestionCollection;

abstract class BaseCollection
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
            $stars = $this->makeStars($word);
            $question = $this->createQuestion($this->testID, $word, $stars);
            $collection->addQuestion($question);
        }
    }

    protected function makeStars(array $word)
    {
        $stars = 0;
        $wordID = (int) $word['id'];
        foreach ($this->stars as $star) {
            if ((int)$star['entity_id'] === $wordID && (int)$star['answer_entity_id'] === $this->getCorrectValue($word)) {
                $stars = $star['stars'];
                break;
            }
        }
        return $stars;
    }

    abstract public function createQuestion(int $testID, array $word, int $stars);
    abstract public function getCorrectValue(array $word): int;

}
