<?php

namespace backend\models\question;

use backend\models\question\region\RegionImageFile;
use common\models\StoryTestQuestion;
use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;

class RegionQuestion extends Model
{

    public $test_id;
    public $name;
    public $type;
    public $order;
    public $mix_answers;

    public $imageFile;
    public $regions;

    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['order', 'type', 'mix_answers'], 'integer'],
            [['type'], 'compare', 'compareValue' => QuestionType::REGION, 'type' => 'number'],
            [['name'], 'string', 'max' => 255],
            [['imageFile'], 'image'],
            [['regions'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'test_id' => 'Тест',
            'name' => 'Вопрос',
            'type' => 'Тип',
            'imageFile' => 'Изображение',
        ];
    }

    protected function uploadImage(StoryTestQuestion $model): void
    {
        $uploadedFile = UploadedFile::getInstance($this, 'imageFile');
        if ($uploadedFile !== null) {

            $model->deleteRegionImages();

            $regionImageFile = new RegionImageFile($uploadedFile->extension);

            $folder = $model->getRegionImage()->getImagesPath();
            $imagePath = $regionImageFile->createImageFileName($folder, false);
            $uploadedFile->saveAs($imagePath);

            $miniImagePath = $regionImageFile->createImageFileName($folder);
            Image::resize($imagePath, 640, 480, true)
                ->save($miniImagePath, ['jpeg_quality' => 100]);

            $model->image = $regionImageFile->getFileName();
        }
    }

}