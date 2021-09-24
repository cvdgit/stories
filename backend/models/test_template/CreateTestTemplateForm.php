<?php

namespace backend\models\test_template;

use common\models\StoryTest;
use DomainException;
use yii\base\Model;

class CreateTestTemplateForm extends Model
{

    public $test_id;
    public $title;

    public function rules()
    {
        return [
            [['title', 'test_id'], 'required'],
            ['test_id', 'integer'],
            ['title', 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'test_id' => 'Исходный тест',
            'title' => 'Название',
        ];
    }

    public function create(): int
    {
        if (!$this->validate()) {
            throw new DomainException('CreateTestTemplateForm not valid');
        }
        $sourceTestModel = StoryTest::findOne($this->test_id);
        if ($sourceTestModel === null) {
            throw new DomainException('Source test is null');
        }
        $model = StoryTest::createTemplate($this->title, $sourceTestModel);
        if (!$model->save()) {
            throw new DomainException('Can\'t be saved StoryTest model. Errors: ' . implode(', ', $model->getFirstErrors()));
        }
        return $model->id;
    }
}
