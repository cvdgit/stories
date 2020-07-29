<?php

namespace backend\components\book\blocks;

class Block
{

    public function isEmpty()
    {
        $class = new \ReflectionClass($this);
        $empty = true;
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $value = $property->getValue($this);
                $empty = $empty && empty($value);
            }
        }
        return $empty;
    }

}