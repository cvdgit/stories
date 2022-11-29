<?php

namespace backend\forms;

use backend\components\WordListFormatter;
use common\models\TestWord;
use common\models\TestWordList;
use DomainException;
use yii\base\Model;

class WordForm extends Model
{
    public $name;
    public $correct_answer;

    public function __construct(TestWord $model = null, $config = [])
    {
        parent::__construct($config);
        if ($model !== null) {
            $this->name = $model->name;
            $this->correct_answer = $model->correct_answer;
        }
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name', 'correct_answer'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels(): array
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
