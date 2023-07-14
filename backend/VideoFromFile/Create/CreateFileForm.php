<?php

declare(strict_types=1);

namespace backend\VideoFromFile\Create;

use yii\base\Model;

class CreateFileForm extends Model
{
    public $title;
    public $videoFile;
    public $captions;

    public function rules(): array
    {
        return [
            [['title', 'videoFile'], 'required'],
            [['title'], 'string', 'max' => 255],
            ['videoFile', 'file', 'extensions' => 'mp4', 'maxSize' => 1024 * 1024 * 500],
            ['captions', 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'title' => 'Название',
            'videoFile' => 'Файл с видео (mp4)',
            'captions' => 'Субтитры',
        ];
    }
}
