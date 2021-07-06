<?php

namespace backend\models\editor;

use common\models\StorySlide;
use yii\base\Model;

class SlideSourceForm extends Model
{

    public $source;
    public $slide_id;

    public function rules()
    {
        return [
            ['slide_id', 'integer'],
            ['source', 'safe'],
        ];
    }

    public function saveSlideSource(StorySlide $slideModel): void
    {
        $slideModel->updateData($this->source);
    }
}
