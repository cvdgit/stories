<?php

namespace backend\models;

use DomainException;
use yii;
use yii\base\Exception;
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

    /**
     * @throws Exception
     */
    public function upload(?string $existsCover = null): string
    {
        if (!$this->validate()) {
            throw new DomainException('Cover is not valid');
        }

        $fileName = Yii::$app->security->generateRandomString() . '.' . $this->coverFile->extension;
        $saveAsPath = StoryCover::getCoverFolderPath(true) . '/' . $fileName;
        $this->coverFile->saveAs($saveAsPath);

        if ($existsCover !== null) {
            StoryCover::delete($existsCover);
        }
        StoryCover::create($saveAsPath);

        return $fileName;
    }
}
