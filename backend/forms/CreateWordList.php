<?php

namespace backend\forms;

use common\models\TestWordList;
use yii\base\Model;

class CreateWordList extends Model
{

    public $name;
    public $story;

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

    public function createWordList()
    {
        if (!$this->validate()) {
            throw new \DomainException('Model not valid');
        }
        $model = TestWordList::create($this->name);
        if ($this->story !== null) {
            $model->stories = [$this->story];
        }
        $model->save();
        return $model->id;
    }

}