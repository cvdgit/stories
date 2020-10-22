<?php

namespace backend\models;

use common\models\TestWord;
use yii\base\Model;

class WordListAsTextForm extends Model
{

    public $word_list_id;
    public $text;

    public function rules()
    {
        return [
            ['text', 'required'],
            ['word_list_id', 'integer'],
            ['text', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'text' => 'Текст',
        ];
    }

    public function createWordList()
    {
        TestWord::clearWords($this->word_list_id);
        $texts = explode(PHP_EOL, $this->text);
        $rows = array_map(static function($row) {
            @list($text, $correctAnswer) = explode('|', $row);
            $text = trim(preg_replace('/[^\w\-\s.,!?]/u', '', $text));
            $correctAnswer = trim(preg_replace('/[^\w\-\s.,]/u', '', $correctAnswer));
            return [
                'name' => $text,
                'correct_answer' => $correctAnswer,
            ];
        }, $texts);
        TestWord::createBatch($this->word_list_id, $rows);
    }

}
