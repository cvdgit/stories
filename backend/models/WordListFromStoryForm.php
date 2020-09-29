<?php

namespace backend\models;

use common\models\Story;
use common\models\TestWord;
use common\models\TestWordList;
use yii\base\Model;

class WordListFromStoryForm extends Model
{

    public $story_id;
    public $text;

    public function rules()
    {
        return [
            ['text', 'required'],
            ['story_id', 'integer'],
            ['text', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'text' => 'Текст',
        ];
    }

    public function createWordList(Story $story)
    {
        $wordList = TestWordList::create($story->title);
        $wordList->save();
        TestWord::createBatch($wordList->id, explode(PHP_EOL, $this->text));
        return $wordList->id;
    }

}