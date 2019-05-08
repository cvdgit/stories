<?php


namespace backend\models;


use yii\base\Model;

class StoryBatchCommandForm extends Model
{
    public $command;
    public $story_ids;

    public function rules()
    {
        return [
            [['command', 'story_ids'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'command' => 'Команда',
        ];
    }

}