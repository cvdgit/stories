<?php

namespace backend\models\test;

use common\models\test\AnswerType;
use common\models\TestWordList;
use yii\base\Model;

class CreateStoryForm extends Model
{

    public $word_list_id;

    public $test_name;
    public $test_answer_type;

    public $story_name;

    private $wordList;

    public function __construct(TestWordList $wordList, $config = [])
    {
        $this->wordList = $wordList;
        $this->word_list_id = $wordList->id;
        $this->test_name = $wordList->name;
        $this->story_name = $wordList->name;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['test_name', 'story_name', 'test_answer_type', 'word_list_id'], 'required'],
            [['test_name', 'story_name'], 'string', 'max' => 255],
            [['test_answer_type'], 'in', 'range' => array_keys(AnswerType::asArray())],
            [['word_list_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'test_name' => 'Название теста',
            'test_answer_type' => 'Тип ответов',
            'story_name' => 'Название истории',
        ];
    }

    private function createTest()
    {

    }

    private function createStory()
    {

    }

    public function create()
    {
        if (!$this->validate()) {
            throw new \DomainException('Not valid');
        }
    }

}