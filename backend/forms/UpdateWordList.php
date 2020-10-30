<?php

namespace backend\forms;

use common\models\TestWordList;
use DomainException;
use yii\base\Model;

class UpdateWordList extends Model
{

    public $id;
    public $name;
    public $story;

    private $model;

    public function __construct(TestWordList $model, $config = [])
    {
        $this->model = $model;
        $this->loadModelAttributes();
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['story'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Заголовок',
            'story' => 'История',
        ];
    }

    private function loadModelAttributes()
    {
        foreach ($this->getAttributes() as $name => $value) {
            $modelAttributes = $this->model->getAttributes();
            if (isset($modelAttributes[$name])) {
                $this->{$name} = $this->model->{$name};
            }
        }
        if (!empty($this->model->stories)) {
            $this->story = $this->model->stories[0]->id;
        }
    }

    public function getTestWordsAsArray()
    {
        return $this->model->getTestWordsAsArray();
    }

    public function updateWordList()
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }
        foreach ($this->getAttributes() as $name => $value) {
            $modelAttributes = $this->model->getAttributes();
            if (isset($modelAttributes[$name])) {
                $this->model->{$name} = $this->{$name};
            }
        }
        if (!empty($this->story)) {
            $this->model->stories = [$this->story];
        }
        $this->model->save();
    }

}