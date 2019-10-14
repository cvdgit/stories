<?php

namespace backend\models\audio;

use common\models\BaseAudioTrackModel;
use common\models\StoryAudioTrack;

class CreateAudioForm extends BaseAudioTrackModel
{

    /** @var AudioUploadForm */
    public $audioUploadForm;

    public function __construct(int $storyID, int $userID, $config = [])
    {
        $this->story_id = $storyID;
        $this->user_id = $userID;
        $this->audioUploadForm = new AudioUploadForm($this->story_id);
        parent::__construct($config);
    }

    public function uploadTrackFiles(int $trackID)
    {
        $this->audioUploadForm->audioTrackID = $trackID;
        return $this->audioUploadForm->upload();
    }

    public function createTrack()
    {
        $model = StoryAudioTrack::create($this->name, $this->story_id, $this->user_id, $this->type, $this->default);
        $model->save();
        return $model->id;
    }

}