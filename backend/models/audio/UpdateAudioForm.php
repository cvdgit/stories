<?php

namespace backend\models\audio;

use common\models\BaseAudioTrackModel;
use common\models\StoryAudioTrack;

class UpdateAudioForm extends BaseAudioTrackModel
{

    /** @var AudioUploadForm */
    public $audioUploadForm;

    public $trackID;

    private $_track;

    public function __construct(int $trackID, $config = [])
    {
        $this->trackID = $trackID;
        $this->loadTrack();
        $this->audioUploadForm = new AudioUploadForm($this->story_id);
        $this->audioUploadForm->audioTrackID = $this->trackID;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['type', 'name'], 'required'],
            [['type', 'default'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function getTrack()
    {
        if ($this->_track === null) {
            $this->_track = StoryAudioTrack::findModel($this->trackID);
        }
        return $this->_track;
    }

    public function loadTrack()
    {
        $model = $this->getTrack();
        $this->story_id = $model->story_id;
        $this->user_id = $model->user_id;
        $this->name = $model->name;
        $this->type = $model->type;
        $this->default = $model->default;
    }

    public function updateTrack()
    {
        $model = $this->getTrack();
        $model->name = $this->name;
        $model->type = $this->type;
        $model->default = $this->default;
        return $model->save();
    }

    public function uploadTrackFiles()
    {
        return $this->audioUploadForm->upload();
    }

}