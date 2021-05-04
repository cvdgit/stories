<?php

namespace backend\components\question\base;

class Question
{

    private $id;
    private $type;
    private $name;

    private $answers = [];

    public function __construct(int $id, int $type, string $name)
    {
        $this->id = $id;
        $this->type = $type;
        $this->name = $name;
    }

}