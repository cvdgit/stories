<?php


namespace common\services;


use common\models\Story;
use common\models\StoryAudioTrack;
use common\models\StorySlide;
use frontend\models\SlideAudio;
use http\Exception\RuntimeException;
use Yii;

class StoryAudioService
{

    public function createTrack(string $name, int $storyID, int $userID, int $type, int $default)
    {
        $model = StoryAudioTrack::create($name, $storyID, $userID, $type, $default);
        $model->save();
        return $model->id;
    }

    public function getTrackRelativePath(int $storyID, int $trackID)
    {
        return '/audio/' . $storyID . DIRECTORY_SEPARATOR . $trackID;
    }

    public function getTrackPath(int $storyID, int $trackID)
    {
        return Yii::getAlias('@public') . $this->getTrackRelativePath($storyID, $trackID);
    }

    public function createTrackFolder(string $folder)
    {
        if (!file_exists($folder)) {
            if (!mkdir($concurrentDirectory = $folder) && !is_dir($concurrentDirectory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }
    }

    public function joinWavs($wavs)
    {
        $fields = implode('/', ['H8ChunkID', 'VChunkSize', 'H8Format', 'H8Subchunk1ID', 'VSubchunk1Size', 'vAudioFormat', 'vNumChannels', 'VSampleRate', 'VByteRate', 'vBlockAlign', 'vBitsPerSample']);
        $data = '';
        $header = '';
        foreach ($wavs as $wav) {
            $fp = fopen($wav,'rb');
            $header = fread($fp,36);
            $info = unpack($fields, $header);
            if ($info['Subchunk1Size'] > 16) {
                $header .= fread($fp, ($info['Subchunk1Size'] - 16));
            }
            $header .= fread($fp,4);
            $size  = unpack('Vsize', fread($fp,4));
            $size  = $size['size'];
            $data .= fread($fp, $size);
        }
        return $header . pack('V', strlen($data)) . $data;
    }

    public function setSlideAudio(SlideAudio $model)
    {
        $slideModel = StorySlide::findSlide($model->slide_id);

        $data = $this->joinWavs($model->getFiles());

        $audioFileName = $this->getTrackPath($slideModel->story_id, $model->track_id);
        $this->createTrackFolder($audioFileName);
        file_put_contents($audioFileName . DIRECTORY_SEPARATOR . ($slideModel->number - 1) . '.0.mp3', $data);

        foreach ($model->getFiles() as $fileName) {
            if (file_exists($fileName)) {
                unlink($fileName);
            }
        }
    }

    public function getStoryTrack(Story $story, $trackID = null)
    {
        if ($trackID === null) {
            $track = array_filter($story->storyAudioTracks, function(StoryAudioTrack $model) {
                return $model->isDefault();
            });
            if (count($track) > 0) {
                $track = $track[0];
            }
            else {
                $track = null;
            }
        }
        else {
            $track = StoryAudioTrack::findModel($trackID);
        }
        return $track;
    }

    public function getStoryTrackPath(int $storyID, int $trackID): string
    {
        return $this->getTrackRelativePath($storyID, $trackID);
    }

}