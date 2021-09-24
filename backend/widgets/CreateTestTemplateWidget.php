<?php

namespace backend\widgets;

use backend\models\test_template\CreateTestTemplateForm;
use yii\base\Widget;

class CreateTestTemplateWidget extends Widget
{

    public $testId;

    public function run()
    {
        $model = new CreateTestTemplateForm();
        $model->test_id = $this->testId;
        return $this->render('create-test-template', [
            'model' => $model,
        ]);
    }
}
