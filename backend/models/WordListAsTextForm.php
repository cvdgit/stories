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
        $rows = explode(PHP_EOL, $this->text);
        array_map(function($row) {
            $row = trim(preg_replace('/[^A-ZА-Я0-9\-]/ui', '', $row));
            return $row;
        }, $rows);
        TestWord::createBatch($this->word_list_id, $rows);
    }

}
