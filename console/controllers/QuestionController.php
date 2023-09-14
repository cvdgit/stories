<?php

namespace console\controllers;

use backend\models\question\QuestionType;
use common\models\StoryTestQuestion;
use yii\console\Controller;

class QuestionController extends Controller
{
    public function actionPassTestWeight(): void
    {
        $models = StoryTestQuestion::findAll(['type' => QuestionType::PASS_TEST]);
        echo count($models) . PHP_EOL;
        echo "Done!" . PHP_EOL;
    }
}
