<?php

namespace backend\models\question;

use common\models\StoryTest;
use common\models\StoryTestQuestion;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;

class QuestionModel extends Model
{

    public $story_test_id;
    public $name;
    public $type;
    public $order;
    public $mix_answers;
    public $imageFile;

    public function rules()
    {
        return [
            [['name', 'type', 'story_test_id'], 'required'],
            [['order', 'type', 'mix_answers', 'story_test_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['imageFile'], 'image'],
            [['story_test_id'], 'exist', 'targetClass' => StoryTest::class, 'targetAttribute' => ['story_test_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'story_test_id' => 'Тест',
            'name' => 'Вопрос',
            'type' => 'Тип',
            'imageFile' => 'Изображение',
            'mix_answers' => 'Перемешивать ответы',
        ];
    }

    protected function uploadImage(StoryTestQuestion $model)
    {
        $uploadedFile = UploadedFile::getInstance($this, 'imageFile');
        if ($uploadedFile !== null) {

            $folder = $model->getImagesPath();
            FileHelper::createDirectory($folder);

            $oldImageFileName = $model->image;

            $fileName = Yii::$app->security->generateRandomString() . '.' . $uploadedFile->extension;
            $imagePath = $folder . $fileName;
            $uploadedFile->saveAs($imagePath);

            $thumbImagePath = $folder . 'thumb_' . $fileName;
            Image::resize($imagePath, 330, 500)->save($thumbImagePath, ['quality' => 100]);

            if (!empty($oldImageFileName)) {
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

            $model->image = 'thumb_' . $fileName;
        }
    }

}