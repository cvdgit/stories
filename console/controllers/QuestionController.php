<?php

namespace console\controllers;

use backend\models\question\QuestionType;
use common\models\StoryTestQuestion;
use yii\console\Controller;
use yii\db\Expression;
use yii\helpers\Json;

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

        $i = 0;
        foreach ($models as $model) {
            $json = Json::decode($model->regions);
            if (count($json['fragments']) > 1) {
                $i++;
            }
        }
        echo $i . PHP_EOL;
        echo "Done!" . PHP_EOL;
    }
}
