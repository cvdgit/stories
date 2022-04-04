<?php

namespace backend\components;

class TestTypeOptions
{

    private $type;

    public function __construct(int $type)
    {
        $this->type = $type;
    }

    public function forGroup(array $types): array
    {
        $options = ['options' => ['data-types' => implode(',', $types)]];
        if (!in_array($this->type, $types, true)) {
            $options['options']['class'] = 'form-group hide';
        }
        return $options;
    }

    public function forField(array $types): array
    {
        $options = [];
        if (!in_array($this->type, $types, true)) {
            $options = ['disabled' => true];
        }
        return $options;
    }
}