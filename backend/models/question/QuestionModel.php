<?php

namespace backend\models\question;

use common\models\StoryTestQuestion;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class QuestionModel extends Model
{

    public $test_id;
    public $name;
    public $type;
    public $order;
    public $mix_answers;
    public $imageFile;

    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['order', 'type', 'mix_answers'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['imageFile'], 'image'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'test_id' => 'Тест',
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
            $fileName = Yii::$app->security->generateRandomString() . '.' . $uploadedFile->extension;
            $folder = $model->getImagesPath();
            FileHelper::createDirectory($folder);
            $uploadedFile->saveAs($folder . $fileName);
            $model->image = $fileName;
        }
    }

}