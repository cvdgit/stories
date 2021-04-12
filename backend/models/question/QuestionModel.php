<?php

namespace backend\models\question;

use common\models\StoryTest;
use common\models\StoryTestQuestion;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
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
            $fileName = Yii::$app->security->generateRandomString() . '.' . $uploadedFile->extension;
            $folder = $model->getImagesPath();
            FileHelper::createDirectory($folder);
            $uploadedFile->saveAs($folder . $fileName);
            $model->image = $fileName;
        }
    }

}