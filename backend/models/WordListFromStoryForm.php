<?php

namespace backend\models;

use backend\components\WordListFormatter;
use common\models\Story;
use common\models\TestWord;
use common\models\TestWordList;
use yii\base\Model;

class WordListFromStoryForm extends Model
{

    public $story_id;
    public $text;

    private $wordFormatter;

    public function __construct($config = [])
    {
        $this->wordFormatter = new WordListFormatter();
        parent::__construct($config);
    }

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
        $wordList->stories = [$story];
        $wordList->save();

        $texts = explode(PHP_EOL, $this->text);
        $rows = $this->wordFormatter->create($texts);
        TestWord::createBatch($wordList->id, $rows);

        return $wordList->id;
    }

}