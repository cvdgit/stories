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

    /** @var int */
    public $track_id;

    /** @var UploadedFile[] */
    public $slide_audio_files;

    protected $files = [];

    public function rules()
    {
        return [
            [['slide_id', 'track_id'], 'integer'],
            ['slide_audio_files', 'file', 'skipOnEmpty' => true, 'maxFiles' => 50],
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
        if ($this->validate() && $this->slide_audio_files !== null) {
            $slideAudioFolder = $this->slideAudioFilePath();
            if (!file_exists($slideAudioFolder)) {
                $this->createFolder($slideAudioFolder);
            }
            foreach ($this->slide_audio_files as $file) {
                $fileName = $slideAudioFolder . '/' . $this->slide_id . '-' . microtime() . '.wav';
                $file->saveAs($fileName);
                $this->files[] = $fileName;
            }
            return true;
        }
        return false;
    }

    public function getFiles()
    {
        return $this->files;
    }

}