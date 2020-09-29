<?php

namespace backend\models;

use common\models\TestWord;
use DomainException;
use yii\base\Model;

class UpdateWordForm extends Model
{

    public $name;

    private $model;

    public function __construct(TestWord $model, $config = [])
    {
        $this->model = $model;
        $this->loadModelAttributes();
        parent::__construct($config);
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Слово',
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
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function updateWord()
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }

        foreach ($this->getAttributes() as $name => $value) {
            $this->model->{$name} = $this->{$name};
        }
        $this->model->save();
    }

    public function copyWord()
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }

        $model = TestWord::create($this->name, $this->model->word_list_id, 1);
        $model->save();
    }

}