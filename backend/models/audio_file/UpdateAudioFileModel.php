<?php

namespace backend\models\audio_file;

use common\models\AudioFile;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UpdateAudioFileModel extends Model
{

    public $name;
    public $audio_file_name;

    /** @var UploadedFile */
    public $audio_file;

    private $audioFileModel;

    public function __construct(AudioFile $audioFileModel, $config = [])
    {
        $this->audioFileModel = $audioFileModel;
        $this->loadAttributes();
        parent::__construct($config);
    }

    private function loadAttributes(): void
    {
        $this->name = $this->audioFileModel->name;
        $this->audio_file_name = $this->audioFileModel->audio_file;
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

    public function getAudioFileUrl(): string
    {
        return $this->audioFileModel->getAudioFileUrl();
    }
}
