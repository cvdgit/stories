<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\Testing\ImportQuestions\Form\FormAction;
use backend\Testing\ImportQuestions\Import\ImportAction;
use backend\Testing\ImportQuestions\Questions\QuestionsAction;
use backend\Testing\ImportQuestions\SelectTest\SelectTestAction;
use yii\web\Controller;

class QuestionsImportController extends Controller
{
    public function actions(): array
    {
        return [
            'form' => FormAction::class,
            'questions' => QuestionsAction::class,
            'import' => ImportAction::class,
            'select-test' => SelectTestAction::class,
        ];
    }
}
