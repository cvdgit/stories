<?php

namespace common\components\module\routes;

final class Group
{
    public $rules;
    public $priority;

    public function __construct(array $rules, int $priority)
    {
        $this->rules = $rules;
        $this->priority = $priority;
    }
}
