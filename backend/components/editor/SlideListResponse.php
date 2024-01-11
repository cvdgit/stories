<?php

declare(strict_types=1);

namespace backend\components\editor;

use backend\components\SlideModifier;
use common\models\StorySlide;
use Exception;

class SlideListResponse
{
    private $slide;
    private $slideModifier;

    public function __construct(StorySlide $slide)
    {
        $this->slide = $slide;

        $slideData = '';
        try {
            $slideData = $slide->getSlideOrLinkData();
        } catch (Exception $ex) {
        }

        $this->slideModifier = new SlideModifier($slide->id, $slideData);
    }

    public function asArray(): array
    {
        $s = $this->slide;
        $slideData = $this->slideModifier
            ->addImageId()
            ->addDescription()
            ->render();
        return [
            'id' => $s->id,
            'slideNumber' => $s->number,
            'isLink' => $s->isLink(),
            'isQuestion' => $s->isQuestion(),
            'linkSlideID' => $s->link_slide_id,
            'isHidden' => $s->isHidden(),
            'data' => $slideData,
            'status' => $s->status,
            'haveLinks' => (count($s->storySlideBlocks) > 0),
            'number' => $s->number,
            'haveNeoRelations' => (count($s->neoSlideRelations) > 0),
        ];
    }
}
