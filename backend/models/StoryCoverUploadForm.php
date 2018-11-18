<?php

namespace backend\models;

use yii;
use yii\base\Model;
use yii\web\UploadedFile;

class StoryCoverUploadForm extends Model
{
	
	/**
     * @var UploadedFile
     */
    public $coverFile;

    public function rules()
    {
        return [
            [['coverFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'coverFile' => 'Обложка',
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
        	$fileName = Yii::$app->security->generateRandomString() . '.' . $this->coverFile->extension;
        	$saveAsPath = Yii::getAlias('@public') . '/slides_cover/' . $fileName;
            $this->coverFile->saveAs($saveAsPath);
            $this->coverFile = $fileName;
            return true;
        } else {
            return false;
        }
    }
}