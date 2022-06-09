<?php

namespace backend\services;

use backend\components\WordListFormatter;
use backend\models\question\sequence\SequenceAnswerForm;
use common\models\StoryTestAnswer;
use common\services\TransactionManager;
use DomainException;

class SequenceAnswerService
{

    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function create(int $questionId, SequenceAnswerForm $form): array
    {
        if (!$form->validate()) {
            throw new DomainException('SequenceAnswerForm not valid');
        }
        $forms = [];
        if ($form->typeIsFull()) {
            $forms[] = $form;
        }
        else {
            $words = WordListFormatter::stringAsWords($form->name);
            foreach ($words as $order => $word) {
                $forms[] = SequenceAnswerForm::create($word, $order);
            }
        }
        $this->transactionManager->wrap(static function() use ($questionId, $forms) {
            foreach ($forms as $i => $form) {
                $model = StoryTestAnswer::createSequenceAnswer($questionId, $form->name);
                if (!$model->save()) {
                    throw new DomainException('StoryTestAnswer save exception');
                }
                $forms[$i]->id = $model->id;
            }
        });
        return $forms;
    }
}
