<?php

namespace backend\components\question\base;

class Answer
{

    private $id;
    private $name;
    private $correct;

    public function __construct(int $id, string $name, bool $correct)
    {
        $this->id = $id;
        $this->name = $name;
        $this->correct = $correct;
    }

}