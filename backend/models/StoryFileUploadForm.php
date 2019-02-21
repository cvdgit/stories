<?php

namespace backend\models;

use yii;
use yii\base\Model;
use yii\web\UploadedFile;

class StoryFileUploadForm extends Model
{
	
	/**
     * @var UploadedFile
     */
    public $storyFile;

    public function rules()
    {
        return [
            [['storyFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pptx', 'maxSize' => 50 * 1024 * 1024],
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

    public function upload()
    {
        if ($this->validate()) {
        	$fileName = Yii::$app->security->generateRandomString() . '.' . $this->storyFile->extension;
        	$saveAsPath = Yii::getAlias('@public') . '/slides_file/' . $fileName;
            $this->storyFile->saveAs($saveAsPath);
            $this->storyFile = $fileName;
            return true;
        } else {
            return false;
        }
    }
}