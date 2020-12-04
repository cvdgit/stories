<?php

namespace backend\models;

use yii;
use yii\base\Model;
use common\components\StoryCover;

class StoryCoverUploadForm extends Model
{
	
	/**
     * @var UploadedFile
     */
    public $coverFile;

    public function rules()
    {
        return [
            [['coverFile'], 'file', 'skipOnEmpty' => true, 'checkExtensionByMimeType' => false, 'extensions' => 'png, jpg, jfif'],
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

    public function upload($existsCover)
    {
        if (!$this->validate()) {
            throw new \DomainException('Cover is not valid');
        }

        $fileName = Yii::$app->security->generateRandomString() . '.' . $this->coverFile->extension;
        $saveAsPath = StoryCover::getCoverFolderPath(true) . '/' . $fileName;
        $this->coverFile->saveAs($saveAsPath);

        if (!empty($existsCover)) {
            StoryCover::delete($existsCover);
        }
        StoryCover::create($saveAsPath);

        return $fileName;

    }
}