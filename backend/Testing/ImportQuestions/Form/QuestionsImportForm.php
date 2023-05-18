<?php

declare(strict_types=1);

namespace backend\Testing\ImportQuestions\Form;

use yii\base\Model;

class QuestionsImportForm extends Model
{
    public $from_test_id;
    public $to_test_id;
    public $questions;

    public function rules(): array
    {
        return [
            [['from_test_id', 'to_test_id', 'questions'], 'required'],
            [['from_test_id', 'to_test_id'], 'integer'],
            ['questions', 'each', 'rule' => ['integer']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'from_test_id' => 'Из теста',
        ];
    }
}
