<?php

namespace backend\components\training\neo;

use backend\components\training\base\BaseQuestion;

class Question extends BaseQuestion
{

    private $entityID;
    private $entityName;
    private $relationID;
    private $relationName;
    private $topicID;
    private $topicName;
    private $view;
    private $svg;

    public function __construct(int $id, string $name, bool $lastAnswerIsCorrect, $image = null)
    {
        parent::__construct($id, $name, $lastAnswerIsCorrect, $image);

    }

}