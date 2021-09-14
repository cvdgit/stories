<?php

namespace backend\helpers;

use backend\components\SlideModifier;
use common\models\StorySlide;

class SelectSlideWidgetHelper
{

    public static function getSlides(array $slides): array
    {
        return array_map(static function(StorySlide $slide) {
            $data = (new SlideModifier($slide->id, $slide->data))
                ->addDescription()
                ->render();
            return [
                'id' => $slide->id,
                'slideNumber' => $slide->number,
                'data' => $data,
                'story' => $slide->isRelationPopulated('story') ? $slide->story->title : ''
            ];
        }, $slides);
    }
}
