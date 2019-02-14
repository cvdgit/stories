<?php

namespace backend\models;

use yii;
use common\models\Story;

class SourcePowerPointForm extends yii\base\Model
{

    public $storyFile;
    public $firstSlideTemplate = 1;
    public $lastSlideTemplate = 1;
    public $storyId;

    public function rules()
    {
        return [
            [['storyFile'], 'string'],
            [['firstSlideTemplate', 'lastSlideTemplate'], 'safe'],
            [['storyId'], 'integer'],
            [['storyId'], 'storyExists'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'storyFile' => 'Файл PowerPoint',
            'firstSlideTemplate' => 'Особый шаблон первого слайда',
            'lastSlideTemplate' => 'Особый шаблон последнего слайда',
        ];
    }

    public function storyExists($attribute, $params)
    {
        $id = $this->$attribute;
        if (Story::findOne($id) !== null) {
            $this->addError($attribute, 'История не найдена');
        }
    }

    public function saveSource($body)
    {
        $story = Story::findOne($this->storyId);
        $story->body = $body;
        $story->save(false, ['body']);
    }

}