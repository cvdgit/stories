<?php

namespace backend\components\training\local;

class RememberQuestion extends WordQuestion
{

    private $rememberAnswer;

    public function __construct(int $questionID, string $name, array $stars, bool $remember)
    {
        $this->rememberAnswer = $remember;
        parent::__construct($questionID, $name, $stars);
    }

    public function serialize()
    {
        return array_merge([
            'rememberAnswer' => $this->rememberAnswer,
        ], parent::serialize());
    }

}