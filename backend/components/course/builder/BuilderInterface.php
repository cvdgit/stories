<?php

namespace backend\components\course\builder;

interface BuilderInterface
{
    public function build(array $models): LessonCollectionInterface;
}
