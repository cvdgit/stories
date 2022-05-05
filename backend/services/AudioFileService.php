<?php

namespace backend\services;

use backend\models\audio_file\UpdateAudioFileModel;
use common\models\AudioFile;
use DomainException;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class AudioFileService
{

    public function updateAudioFile(AudioFile $audioFile, UpdateAudioFileModel $form): void
    {
        if (!$form->validate()) {
            throw new DomainException('UpdateAudioFileModel not valid');
        }

        $audioFile->updateName($form->name);

        if ($form->audio_file !== null) {
            $this->deleteAudioFile($audioFile);
            $fileName = $this->uploadAudioFile($audioFile, $form->audio_file);
            $audioFile->updateAudioFile($fileName);
        }

        if (!$audioFile->save()) {
            throw new DomainException('AudioFile save exception');
        }
    }

    public function uploadAudioFile(AudioFile $audioFile, UploadedFile $file): string
    {
        $audioFolder = AudioFile::getAudioFilesPath($audioFile->folder);
        if (!file_exists($audioFolder)) {
            if (!mkdir($audioFolder, 0755, true) && !is_dir($audioFolder)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $audioFolder));
            }
        }
        $fileName = $file->baseName . '.' . $file->extension;
        $file->saveAs($audioFolder . '/' . $fileName);
        return $fileName;
    }

    public function deleteAudioFile(AudioFile $audioFile): void
    {
        if (($path = $audioFile->getAudioFilePath()) !== null && file_exists($path)) {
            FileHelper::unlink($path);
        }
    }
}
