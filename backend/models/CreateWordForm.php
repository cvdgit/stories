<?php

namespace backend\models;

use common\models\TestWord;
use common\models\TestWordList;
use DomainException;
use yii\base\Model;

class CreateWordForm extends Model
{

    public $name;

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
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Слово',
        ];
    }

    public function getTestWordsAsArray()
    {
        return $this->list->getTestWordsAsArray();
    }

    public function createWord()
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }
        $model = TestWord::create($this->name, $this->list->id, 1);
        $model->save();
    }

}