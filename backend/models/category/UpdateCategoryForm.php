<?php

namespace backend\models\category;

use common\models\Category;

class UpdateCategoryForm extends BaseCategoryForm
{

    private $model;

    public function __construct(Category $model, $config = [])
    {
        $this->model = $model;
        $this->loadAttributes();
        parent::__construct($config);
    }

    private function loadAttributes(): void
    {
        if ($this->model === null) {
            return;
        }
        $this->name = $this->model->name;
        $this->alias = $this->model->alias;
        $this->description = $this->model->description;
        $this->sort_field = $this->model->sort_field;
        $this->sort_order = $this->model->sort_order;
        $parent = $this->model->parents(1)->one();
        if ($parent !== null) {
            $this->parent = $parent->id;
        }
    }

    public function getModelID()
    {
        return $this->model->id;
    }

    public function updateCategory(): void
    {
        if (!$this->validate()) {
            throw new \DomainException('UpdateCategoryForm is not valid');
        }
        $this->model->name = $this->name;
        $this->model->alias = $this->alias;
        $this->model->description = $this->description;
        $this->model->sort_field = $this->sort_field;
        $this->model->sort_order = $this->sort_order;
        $this->model->save();
    }
}