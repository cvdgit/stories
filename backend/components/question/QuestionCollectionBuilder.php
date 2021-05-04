<?php

namespace backend\components\question;

abstract class QuestionCollectionBuilder
{

    abstract public function createQuestion();

    public function build()
    {
        $collection = new QuestionCollection();
/*        foreach ($this->data as $questionData) {
            $question = $this->createQuestion($questionData, $this->stars);
            $collection->addQuestion($question);
        }*/
        return $collection;
    }

}