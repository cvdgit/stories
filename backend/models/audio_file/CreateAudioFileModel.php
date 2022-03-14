<?php

namespace backend\models\audio_file;

use common\models\AudioFile;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class CreateAudioFileModel extends Model
{

    public $name;
    public $audio_file_name;

    /** @var UploadedFile */
    public $audio_file;

    private $folder;

    public function init()
    {
        $this->folder = Yii::$app->formatter->asDate('now', 'MM-yyyy');
        parent::init();
    }

    public function rules(): array
    {
        return [
            [['name', 'audio_file_name'], 'required'],
            [['name', 'audio_file_name'], 'string', 'max' => 255],
            ['audio_file', 'file'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Заголовок',
            'audio_file_name' => 'Аудио файл',
        ];
    }

    public function uploadAudioFile(): string
    {
        if (!$this->validate()) {
            throw new \DomainException('CreateAudioFileModel is not valid');
        }
        $audioFolder = AudioFile::getAudioFilesPath($this->folder);
        if (!file_exists($audioFolder)) {
            if (!mkdir($audioFolder, 0755, true) && !is_dir($audioFolder)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $audioFolder));
            }
        }
        $fileName = $this->audio_file->baseName . '.' . $this->audio_file->extension;
        $this->audio_file->saveAs($audioFolder . '/' . $fileName);
        return $fileName;
    }

    public function createAudioFile(string $fileName): AudioFile
    {
        $audioFile = AudioFile::create($this->name, $this->folder, $fileName);
        if (!$audioFile->save()) {
            throw new \DomainException('AudioFile::create exception');
        }
        return $audioFile;
    }
}
