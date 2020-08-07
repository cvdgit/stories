<?php

declare(strict_types=1);

namespace backend\components\book\blocks;

use common\models\SlideVideo;

class Video extends Block
{

    /** @var string */
    protected $videoID;

    public $name;

    public function __construct($videoID)
    {
        $this->videoID = $videoID;
        $this->name = $this->getVideoName();
    }

    protected function getVideoName()
    {
        $video = SlideVideo::findModelByVideoID($this->videoID);
        if ($video !== null) {
            return $video->title;
        }
    }

}