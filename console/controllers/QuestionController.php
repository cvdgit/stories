<?php

namespace console\controllers;

use backend\models\question\QuestionType;
use common\models\StoryTestQuestion;
use yii\console\Controller;
use yii\db\Expression;

class QuestionController extends Controller
{
    public function actionPassTestWeight(): void
    {
        $models = StoryTestQuestion::find()
          ->where([
              'type' => QuestionType::PASS_TEST
          ])
        ->andWhere(['not', ['regions' => null]])
        ->all();
        echo count($models) . PHP_EOL;
        /*foreach ($models as $model) {

        }*/
        echo "Done!" . PHP_EOL;
    }
}
