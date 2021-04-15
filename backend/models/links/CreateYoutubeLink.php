<?php

namespace backend\models\links;

use common\models\StorySlideBlock;

class CreateYoutubeLink extends BaseYoutubeLink
{

    public function __construct(int $slide_id, $config = [])
    {
        $this->slide_id = $slide_id;
        parent::__construct($config);
    }

    public function init()
    {
        $this->type = BlockType::YOUTUBE;
        parent::init();
    }

    public function createLink()
    {
        if (!$this->validate()) {
            throw new \DomainException('CreateYoutubeLink is not valid');
        }
        $this->href = $this->createHref($this->youtube_id, $this->start, $this->end);
        $model = StorySlideBlock::create($this->slide_id, $this->title, $this->href, $this->type);
        return $model->save();
    }
}
