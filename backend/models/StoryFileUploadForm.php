<?php

namespace backend\models;

use common\models\Story;
use yii;
use yii\base\Model;
use yii\web\UploadedFile;

class StoryFileUploadForm extends Model
{

    public $storyFile;

    public function rules()
    {
        return [
            [['storyFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pptx', 'maxSize' => 120 * 1024 * 1024],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'storyFile' => 'Файл PowerPoint',
        ];
    }

    public function uploadFile(Story $storyModel): void
    {
        $this->storyFile = UploadedFile::getInstance($this, 'storyFile');
        if (!$this->validate()) {
            throw new \DomainException('Story file is not valid');
        }
        if (!empty($this->storyFile)) {

            if (!empty($storyModel->story_file)) {
                if (file_exists($path = $storyModel->getStoryFilePath())) {
                    unlink($path);
                }
                if (file_exists($path = $storyModel->getSlideImagesPath()) && is_dir($path)) {
                    array_map('unlink', glob($path . DIRECTORY_SEPARATOR . '*.*'));
                    rmdir($path);
                }
                $command = Yii::$app->db->createCommand();
                $command->delete('{{%story_slide}}', 'story_id = :story', [':story' => $storyModel->id])->execute();
            }

            $fileName = Yii::$app->security->generateRandomString() . '.' . $this->storyFile->extension;
            $filePath = $storyModel->getStoryFilesFolder() . '/' . $fileName;
            $this->storyFile->saveAs($filePath);

            $storyModel->story_file = $fileName;
        }
    }
}
