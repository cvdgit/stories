<?php

namespace backend\models\question;

use common\models\StoryTestQuestion;
use DomainException;

class CreateRegionQuestion extends RegionQuestion
{

    public function init()
    {
        parent::init();
        $this->type = QuestionType::REGION;
        $this->name = 'Отметьте правильную область на изображении';
    }

    public function create()
    {
        if (!$this->validate()) {
            throw new DomainException('Model is not valid');
        }

        $model = StoryTestQuestion::createRegion($this->test_id, $this->name);
        $this->uploadImage($model);
        $model->save();
    }

}