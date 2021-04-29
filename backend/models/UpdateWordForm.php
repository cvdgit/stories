<?php

namespace backend\models;

use backend\components\WordListFormatter;
use common\models\TestWord;
use DomainException;
use yii\base\Model;

class UpdateWordForm extends Model
{

    public $name;
    public $correct_answer;

    private $model;

    public function __construct(TestWord $model, $config = [])
    {
        parent::__construct($config);
        $this->model = $model;
        $this->loadModelAttributes();
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Слово',
            'correct_answer' => 'Правильный ответ',
        ];
    }

    private function loadModelAttributes()
    {
        foreach ($this->getAttributes() as $name => $value) {
            $this->{$name} = $this->model->{$name};
        }
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'correct_answer'], 'string', 'max' => 255],
        ];
    }

    public function updateWord(WordListFormatter $wordFormatter): void
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }

        $word = $wordFormatter->createOne($this->name, $this->correct_answer);
        $this->model->name = $word['name'];
        $this->model->correct_answer = $word['correct_answer'];
        $this->model->save();
    }

    public function copyWord(WordListFormatter $wordFormatter): void
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }

        $word = $wordFormatter->createOne($this->name, $this->correct_answer);
        $model = TestWord::create($word['name'], $this->model->word_list_id, 1, $word['correct_answer']);
        $model->save();
    }

}