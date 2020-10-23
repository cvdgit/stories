<?php

namespace backend\models;

use backend\components\WordListFormatter;
use common\models\TestWord;
use yii\base\Model;

class WordListAsTextForm extends Model
{

    public $word_list_id;
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
        $rows = $this->wordFormatter->create($texts);
        TestWord::createBatch($this->word_list_id, $rows);
    }

}
