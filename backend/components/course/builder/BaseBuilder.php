<?php

namespace backend\components\course\builder;

abstract class BaseBuilder
{

    protected $lessonBuilder;
    protected $lessonCollection;

    public function __construct(LessonBuilder $lessonBuilder, LessonCollection $lessonCollection)
    {
        $this->lessonBuilder = $lessonBuilder;
        $this->lessonCollection = $lessonCollection;
    }

}
