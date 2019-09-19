<?php


namespace backend\models;


use http\Exception\RuntimeException;
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

    public function audioFileRelativePath()
    {
        return '/audio/' . $this->storyID;
    }

    public function upload()
    {
        if ($this->validate()) {
            $audioFolder = $this->audioFilePath();
            if (!file_exists($audioFolder)) {
                if (!mkdir($concurrentDirectory = $audioFolder) && !is_dir($concurrentDirectory)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                }
            }
            foreach ($this->audioFiles as $file) {
                $file->saveAs($audioFolder . '/' . $file->baseName . '.' . $file->extension);
            }
            return true;
        }
        return false;
    }

    public function audioFileList(): array
    {
        $files = [];
        if (file_exists($this->audioFilePath())) {
            $dir = opendir($this->audioFilePath());
            while (false !== ($filename = readdir($dir))) {
                if (!in_array($filename, array('.', '..'))) {
                    $files[] = $filename;
                }
            }
        }
        return $files;
    }

    public function deleteAudioFile($file)
    {
        $file = preg_replace('/[^A-Za-z0-9 _ .-]/', '', $file);
        $fileName = $this->audioFilePath() . '/' . $file;
        if (file_exists($fileName)) {
            unlink($fileName);
            return true;
        }
        return false;
    }

}