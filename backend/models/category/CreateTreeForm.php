<?php

namespace backend\models\category;

use common\models\Category;
use yii\base\Model;

class CreateTreeForm extends Model
{

    public $name;

    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Название',
        ];
    }

    public function createTree(): void
    {
        if (!$this->validate()) {
            throw new \DomainException('CreateTreeForm is not valid');
        }
        $model = Category::create($this->name, Category::createAlias($this->name));
        $model->makeRoot();
        $model->save();
    }
}