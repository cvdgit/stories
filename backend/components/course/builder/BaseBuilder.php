<?php

namespace backend\components\course\builder;

abstract class BaseBuilder implements BuilderInterface
{

    protected $lessonBuilder;
    protected $lessonCollection;

    public function __construct(LessonBuilderInterface $lessonBuilder = null, LessonCollectionInterface $lessonCollection = null)
    {
        if ($lessonBuilder === null) {
            $this->lessonBuilder = new LessonBuilder();
        }
        if ($lessonCollection === null) {
            $this->lessonCollection = new LessonCollection();
        }
    }
}
