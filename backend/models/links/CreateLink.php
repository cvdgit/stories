<?php

namespace backend\models\links;

use common\models\StorySlideBlock;

class CreateLink extends BaseLink
{

    public function __construct(int $slide_id, $config = [])
    {
        $this->slide_id = $slide_id;
        parent::__construct($config);
    }

    public function init()
    {
        $this->type = BlockType::BUTTON;
        parent::init();
    }

    public function createLink()
    {
        $model = StorySlideBlock::create($this->slide_id, $this->title, $this->href, $this->type);
        return $model->save();
    }
}
