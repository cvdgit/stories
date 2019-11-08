<?php


namespace backend\models\audio;


use common\models\StorySlide;
use DomainException;
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

    /** @var int */
    public $storyID;

    /** @var int */
    public $audioTrackID;

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
        return Yii::getAlias('@public') . '/audio/' . $this->storyID . DIRECTORY_SEPARATOR . $this->audioTrackID;
    }

    public function audioFileRelativePath()
    {
        return '/audio/' . $this->storyID . DIRECTORY_SEPARATOR . $this->audioTrackID;
    }

    public function upload()
    {
        if ($this->validate()) {
            $audioFolder = $this->audioFilePath();
            if (!file_exists($audioFolder)) {
                if (!mkdir($concurrentDirectory = $audioFolder, 0755, true) && !is_dir($concurrentDirectory)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                }
            }
            foreach ($this->audioFiles as $file) {
                $file->saveAs($audioFolder . '/' . $this->createFileName($file->baseName) . '.' . $file->extension);
            }
            return true;
        }
        return false;
    }

    public function createFileName(string $file)
    {
        $slideNumber = explode('.', $file)[0];
        $slide = StorySlide::findSlideByNumber($this->storyID, $slideNumber);
        if ($slide !== null) {
            return $slide->id;
        }
        return $file;
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
        sort($files, SORT_NUMERIC);
        return $files;
    }

    public function audioFileListBySlideNumber(): array
    {
        $files = [];
        if (file_exists($this->audioFilePath())) {
            $dir = opendir($this->audioFilePath());
            while (false !== ($filename = readdir($dir))) {
                if (!in_array($filename, array('.', '..'))) {
                    $slideID = explode('.', $filename)[0];
                    try {
                        $slide = StorySlide::findSlide($slideID);
                        $files[$slide->number] = $filename;
                    }
                    catch (DomainException $ex) {
                        $files[$slideID] = $filename;
                    }
                }
            }
        }
        ksort($files, SORT_NUMERIC);
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

    public function deleteFiles()
    {
        $audioFolder = $this->audioFilePath();
        if (file_exists($audioFolder)) {
            array_map('unlink', glob($audioFolder . DIRECTORY_SEPARATOR . '*.*'));
            rmdir($audioFolder);
        }
    }

}