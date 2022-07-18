<?php

namespace modules\files\forms;

use yii\base\Model;

class FilesUploadForm extends Model
{

    public $files;

    public function rules(): array
    {
        return [
            ['files', 'required'],
            [['files'], 'file', 'maxFiles' => 0, 'maxSize' => 10 * 1024 * 1024],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'files' => 'Файлы',
        ];
    }
}
