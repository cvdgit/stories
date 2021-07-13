<?php

namespace backend\models\test;

use backend\models\RelatedTests;
use common\models\StoryTest;
use yii\base\Model;

class RelatedTestsForm extends Model
{

    public $test_id;
    public $test_ids = [];

    public function rules()
    {
        return [
            ['test_id', 'integer'],
            ['test_ids', 'each', 'rule' => ['integer']],
        ];
    }

    public function create(StoryTest $testModel): void
    {
        if (!$this->validate()) {
            throw new \DomainException('RelatedTestForm is not valid');
        }
        RelatedTests::deleteByTestID($testModel->id);
        foreach ($this->test_ids as $relatedTestID) {
            $model = RelatedTests::create($testModel->id, $relatedTestID);
            $model->save();
        }
        $testModel->refresh();
    }
}
