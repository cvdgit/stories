<?php


namespace backend\models;


use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class AudioUploadForm extends Model
{

    /**
     * @var UploadedFile[]
     */
    public $audioFiles;

    public $storyID;

    public function __construct(int $storyID, $config = [])
    {
        $this->storyID = $storyID;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            ['audioFiles', 'file', 'skipOnEmpty' => true, 'extensions' => 'mp3', 'maxFiles' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'audioFiles' => 'Файлы озвучки',
        ];
    }

    public function audioFilePath()
    {
        return Yii::getAlias('@public') . '/audio/' . $this->storyID;
    }

    public function upload()
    {
        if ($this->validate()) {
            foreach ($this->audioFiles as $file) {
                $file->saveAs($this->audioFilePath() . '/' . $file->baseName . '.' . $file->extension);
            }
            return true;
        }
        return false;
    }

    public function audioFileList(): array
    {
        $dir = opendir($this->audioFilePath());
        $files = [];
        while (false !== ($filename = readdir($dir))) {
            if (!in_array($filename, array('.', '..'))) {
                $files[] = $filename;
            }
        }
        return $files;
    }

    public function deleteAudioFile($file)
    {
        $fileName = $this->audioFilePath() . '/' . $file;
        if (file_exists($fileName)) {
            unlink($fileName);
        }
    }

}