<?php

namespace backend\models\story_list;

use common\models\StoryList;
use DomainException;
use yii\base\Model;

class CreateStoryListForm extends Model
{

    public $name;
    public $categories = [];

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

    public function create(): int
    {
        if (!$this->validate()) {
            throw new DomainException('CreateStoryListForm is not valid');
        }
        $model = StoryList::create($this->name, $this->categories);
        if (!$model->save()) {
            throw new DomainException('StoryList save error');
        }
        return $model->id;
    }
}