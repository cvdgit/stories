<?php

namespace backend\models\links;

use common\models\StorySlideBlock;

class UpdateYoutubeLink extends BaseYoutubeLink
{

    private $model;

    public function __construct(StorySlideBlock $model, $config = [])
    {
        parent::__construct($config);
        $this->model = $model;
        $this->loadModelAttributes();
    }

    private function loadModelAttributes(): void
    {
        $this->title = $this->model->title;
        $this->slide_id = $this->model->slide_id;
        [$this->youtube_id, $this->start, $this->end] = $this->parseHref($this->model->href);
    }

    private function parseHref(string $href): array
    {
        $matches = [];
        preg_match_all('/embed\/(.*)[\?]+.*start=(\d+)[&]?.*end=(\d+)[&]?/', $href, $matches);
        return [
            $matches[1][0],
            $matches[2][0],
            $matches[3][0],
        ];
    }

    public function updateLink()
    {
        if (!$this->validate()) {
            throw new \DomainException('UpdateYoutubeLink is not valid');
        }
        $model = $this->model;
        $model->title = $this->title;
        $model->href = $this->createHref($this->youtube_id, $this->start, $this->end);
        return $model->save();
    }

    public function getModelID(): int
    {
        return $this->model->id;
    }

}