<?php

namespace backend\models\category;

use common\models\Category;
use yii\base\Model;

class BaseCategoryForm extends Model
{

    public $parent;
    public $name;
    public $alias;
    public $description;
    public $sort_field;
    public $sort_order;
    public $tree;

    public function rules()
    {
        return [
            [['parent', 'sort_order'], 'integer'],
            [['description'], 'string'],
            [['name', 'alias'], 'string', 'max' => 255],
            [['sort_field'], 'string', 'max' => 50],
            ['sort_order', 'default', 'value' => 0],
            ['parent', 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['parent' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Название',
            'alias' => 'Alias',
            'description' => 'Описание',
            'parent' => 'Родительская категория',
            'sort_field' => 'Сортировка',
            'sort_order' => 'Направление сортировки',
        ];
    }

    public function isNewRecord(): bool
    {
        return $this instanceof CreateCategoryForm;
    }

    public function getModelID()
    {

    }
}
