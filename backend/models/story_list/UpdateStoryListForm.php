<?php

namespace backend\models\story_list;

use common\models\StoryList;
use DomainException;
use yii\base\Model;

class UpdateStoryListForm extends Model
{

    public $name;
    public $categories = [];

    private $model;

    public function __construct(StoryList $model, $config = [])
    {
        $this->model = $model;
        $this->name = $model->name;
        $this->categories = $model->categories;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name', 'categories'], 'required'],
            ['name', 'string', 'max' => 50],
            ['categories', 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Название',
            'categories' => 'Категории',
        ];
    }

    public function update(): void
    {
        if (!$this->validate()) {
            throw new DomainException('UpdateStoryListForm validate error');
        }
        $this->model->name = $this->name;
        $this->model->categories = $this->categories;
        if (!$this->model->save()) {
            throw new DomainException('StoryList save error');
        }
    }
}