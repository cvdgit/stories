<?php


namespace frontend\models;


use http\Exception\RuntimeException;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class SlideAudio extends Model
{

    /** @var int */
    public $slide_id;

    /** @var UploadedFile */
    public $slide_audio_file;

    public function rules()
    {
        return [
            ['slide_id', 'integer'],
            ['slide_audio_file', 'file'],
        ];
    }

    public function slideAudioFilePath()
    {
        return Yii::getAlias('@public') . '/user_audio/' . $this->slide_id;
    }

    protected function createFolder($name)
    {
        if (!mkdir($concurrentDirectory = $name) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }

    public function upload()
    {
        if ($this->validate() && $this->slide_audio_file !== null) {
            $slideAudioFolder = $this->slideAudioFilePath();
            if (!file_exists($slideAudioFolder)) {
                $this->createFolder($slideAudioFolder);
            }
            $this->slide_audio_file->saveAs($slideAudioFolder . '/' . $this->slide_audio_file->baseName . '.mp3');
            return true;
        }
        return false;
    }

}