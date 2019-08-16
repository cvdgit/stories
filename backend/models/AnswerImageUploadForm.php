<?php


namespace backend\models;


use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\base\Model;
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

    public function upload()
    {
        if ($this->validate() && $this->answerImage !== null) {
            $fileName = Yii::$app->security->generateRandomString() . '.' . $this->answerImage->extension;
            $filePath = $this->testImagesFilePath() . '/' . $fileName;
            $this->answerImage->saveAs($filePath);
            $this->answerImage = $fileName;

            Image::thumbnail($filePath, 110, 100)->save($filePath, ['jpeg_quality' => 100], ManipulatorInterface::THUMBNAIL_INSET);

            return true;
        }
        return false;
    }

}