<?php

namespace backend\models;

use yii;

class SourceDropboxForm extends yii\base\Model
{

    public $storyFile;
    public $storyId;

    public function rules()
    {
        return [
            [['storyFile'], 'string'],
            [['storyId'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'storyFile' => 'Файл Dropbox',
        ];
    }

    public function saveSource($body)
    {
        $story = Story::findOne($this->storyId);
        $story->body = $body;
        $story->save(false, ['body']);
    }

}