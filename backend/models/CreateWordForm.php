<?php

namespace backend\models;

use backend\components\WordListFormatter;
use common\models\TestWord;
use common\models\TestWordList;
use DomainException;
use yii\base\Model;

class CreateWordForm extends Model
{

    public $name;
    public $correct_answer;

    /** @var TestWordList */
    private $list;

    public function __construct(TestWordList $list, $config = [])
    {
        $this->list = $list;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'correct_answer'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Слово',
            'correct_answer' => 'Правильный ответ',
        ];
    }

    public function getTestWordsAsArray()
    {
        return $this->list->getTestWordsAsArray();
    }

    public function createWord(WordListFormatter $wordFormatter): void
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }

        $word = $wordFormatter->createOne($this->name, $this->correct_answer);
        $model = TestWord::create($word['name'], $this->list->id, 1, $word['correct_answer']);
        $model->save();
    }

}