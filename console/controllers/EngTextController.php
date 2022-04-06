<?php

namespace console\controllers;

use common\components\EngTextNormalizer;
use yii\console\Controller;
use yii\db\Expression;
use yii\db\Query;

class EngTextController extends Controller
{

    public function actionNorm(): void
    {
        $query = (new Query())
            ->select([
                'question_id' => 't2.id',
                'question_name' => 't2.name',
            ])
            ->from(['t' => 'story_test'])
            ->innerJoin(['t2' => 'story_test_question'], 't2.story_test_id = t.id')
            ->where(['t.source' => 1])
            ->andWhere(['t.answer_type' => 0])
            ->andWhere(['t.ask_question' => 1])
            ->andWhere(['t.ask_question_lang' => 'Google US English'])
            ->andWhere(['t2.type' => 0])
            ->andWhere(new Expression("t2.name REGEXP '[А-Яа-я]+'"));
        $rows = $query->all();
        foreach ($rows as $row) {

            $origText = $row['question_name'];
            $text = EngTextNormalizer::normalize($origText);
            $this->stdout($origText . ' - ' . $text . PHP_EOL);
        }

        $this->stdout('Done!' . PHP_EOL);
    }
}