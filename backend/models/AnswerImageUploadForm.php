<?php

namespace backend\models;

use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;

class AnswerImageUploadForm extends Model
{

    /**
     * @var UploadedFile
     */
    public $answerImage;

    public function rules()
    {
        return [
            ['answerImage', 'image', 'skipOnEmpty' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'answerImage' => 'Изображение',
        ];
    }

    public function testImagesFilePath()
    {
        return Yii::getAlias('@public') . '/test_images';
    }

    public function upload($oldImageFileName = null)
    {
        if ($this->validate() && $this->answerImage !== null) {

            $folder = $this->testImagesFilePath() . '/';
            $fileName = Yii::$app->security->generateRandomString() . '.' . $this->answerImage->extension;
            $imagePath = $folder . $fileName;
            $this->answerImage->saveAs($imagePath);

            $thumbImagePath = $folder . 'thumb_' . $fileName;
            Image::thumbnail($imagePath, 110, 100, ManipulatorInterface::THUMBNAIL_INSET)
                ->save($thumbImagePath, ['jpeg_quality' => 100]);

            $this->answerImage = 'thumb_' . $fileName;

            if ($oldImageFileName !== null) {
                $oldImages = [
                    $folder . $oldImageFileName,
                    $folder . 'thumb_' . $oldImageFileName,
                ];
                foreach ($oldImages as $oldImagePath) {
                    if (file_exists($oldImagePath)) {
                        FileHelper::unlink($oldImagePath);
                    }
                }
            }

            return true;
        }
        return false;
    }

}