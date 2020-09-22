<?php

namespace backend\models;

use common\models\TestWord;
use yii\base\Model;

class UpdateWordForm extends Model
{

    public $name;

    private $model;

    public function __construct(TestWord $model, $config = [])
    {
        $this->model = $model;
        parent::__construct($config);
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

    }

}