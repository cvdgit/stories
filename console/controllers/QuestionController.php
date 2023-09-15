<?php

namespace console\controllers;

use backend\models\question\QuestionType;
use backend\services\PassTestService;
use common\models\StoryTestQuestion;
use yii\console\Controller;
use yii\db\Expression;
use yii\helpers\Json;

class QuestionController extends Controller
{
    /**
     * @var PassTestService
     */
    private $passTestService;

    public function __construct($id, $module, PassTestService $passTestService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->passTestService = $passTestService;
    }

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
            /** @var StoryTestQuestion $model */
            $json = Json::decode($model->regions);
            if (isset($json['fragments']) && count($json['fragments']) > 1) {
                $model->weight = $this->passTestService->calcWeight($json);
                $model->save();
            }
        }
        echo $i . PHP_EOL;
        echo "Done!" . PHP_EOL;
    }
}
