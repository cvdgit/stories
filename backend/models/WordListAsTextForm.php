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
        TestWord::createBatch($this->word_list_id, explode(PHP_EOL, $this->text));
    }

}
