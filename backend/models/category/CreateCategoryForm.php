<?php

namespace backend\models\category;

use common\models\Category;

class CreateCategoryForm extends BaseCategoryForm
{

    public $tree;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['parent', 'name'], 'required'],
        ]);
    }

    public function createCategory(): void
    {
        if (!$this->validate()) {
            throw new \DomainException('CreateCategoryForm is not valid');
        }
        $model = Category::create($this->name, $this->alias, $this->description, $this->sort_field, $this->sort_order);
        if ($this->parent !== null) {
            $parent = Category::findOne($this->parent);
            $model->appendTo($parent);
        }
        $model->save();
    }
}