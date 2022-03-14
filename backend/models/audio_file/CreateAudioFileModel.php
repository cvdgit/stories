<?php

namespace backend\models\audio_file;

use common\models\AudioFile;
use common\models\StoryTestQuestion;
use yii\base\Model;
use yii\web\UploadedFile;

class CreateAudioFileModel extends Model
{

    public $question_id;
    public $name;
    public $audio_file_name;

    /** @var UploadedFile */
    public $audio_file;

    public function rules(): array
    {
        return [
            [['question_id', 'name', 'audio_file_name'], 'required'],
            ['question_id', 'integer'],
            [['name', 'audio_file_name'], 'string', 'max' => 255],
            ['question_id', 'exist', 'targetClass' => StoryTestQuestion::class, 'targetAttribute' => ['question_id' => 'id']],
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

    public function uploadAudioFile(string $folder): string
    {
        if (!$this->validate()) {
            throw new \DomainException('CreateAudioFileModel is not valid');
        }
        if (!file_exists($folder)) {
            if (!mkdir($folder, 0755, true) && !is_dir($folder)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $folder));
            }
        }
        $fileName = $this->audio_file->baseName . '.' . $this->audio_file->extension;
        $this->audio_file->saveAs($folder . '/' . $fileName);
        return $fileName;
    }

    public function createAudioFile(string $fileName): AudioFile
    {
        $audioFile = AudioFile::create($this->name, $fileName);
        if (!$audioFile->save()) {
            throw new \DomainException('AudioFile::create exception');
        }
        return $audioFile;
    }

    public function updateQuestion(StoryTestQuestion $questionModel, int $audioFileId): void
    {
        $questionModel->audio_file_id = $audioFileId;
        if (!$questionModel->save()) {
            throw new \DomainException('StoryTestQuestion.save exception');
        }
    }
}