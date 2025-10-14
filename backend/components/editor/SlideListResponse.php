<?php

declare(strict_types=1);

namespace backend\components\editor;

use backend\components\SlideModifier;
use common\models\StorySlide;
use Exception;
use yii\helpers\Url;

class SlideListResponse
{
    private $slide;
    private $slideModifier;
    /**
     * @var bool
     */
    private $haveSlideMentalMaps;

    public function __construct(StorySlide $slide, bool $haveSlideMentalMaps = false)
    {
        $this->slide = $slide;

        $slideData = '';
        try {
            $slideData = $slide->getSlideOrLinkData();
        } catch (Exception $ex) {
        }

        $this->slideModifier = new SlideModifier($slide->id, $slideData);
        $this->haveSlideMentalMaps = $haveSlideMentalMaps;
    }

    public function asArray(): array
    {
        $s = $this->slide;
        $slideData = $this->slideModifier
            ->addImageId()
            ->addDescription()
            ->render();

        $linkUrl = null;
        if ($s->link_slide_id !== null) {
            $linkSlide = StorySlide::findOne($s->link_slide_id);
            if ($linkSlide !== null) {
                $linkUrl = Url::to(['/editor/edit', 'id' => $linkSlide->story_id, '#' => $s->link_slide_id]);
            }
        }

        return [
            'id' => $s->id,
            'slideNumber' => $s->number,
            'isLink' => $s->isLink(),
            'isQuestion' => $s->isQuestion(),
            'linkUrl' => $linkUrl,
            'linkSlideID' => $s->link_slide_id,
            'isHidden' => $s->isHidden(),
            'data' => $slideData,
            'status' => $s->status,
            'haveLinks' => (count($s->storySlideBlocks) > 0),
            'number' => $s->number,
            'haveNeoRelations' => (count($s->neoSlideRelations) > 0),
            'haveMentalMaps' => $this->haveSlideMentalMaps,
        ];
    }
}
