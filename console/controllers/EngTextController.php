<?php

namespace console\controllers;

use common\helpers\EngTextNormalizer;
use common\services\TransactionManager;
use Yii;
use yii\console\Controller;
use yii\db\Expression;
use yii\db\Query;

class EngTextController extends Controller
{

    private $transactionManager;

    public function __construct($id, $module, TransactionManager $transactionManager, $config = [])
    {
        $this->transactionManager = $transactionManager;
        parent::__construct($id, $module, $config);
    }

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

        $commands = [];
        $info = [];
        foreach ($rows as $row) {
            $questionId = $row['question_id'];
            $origText = $row['question_name'];
            $text = EngTextNormalizer::normalize($origText);
            if ($origText !== $text) {

                $info[] = $origText . ' - ' . $text;

                $command = Yii::$app->db->createCommand();
                $command->update('story_test_question', ['name' => $text], 'id = :id', [':id' => $questionId]);
                $commands[] = $command;
            }
        }

        if (count($commands) > 0) {
            $this->transactionManager->wrap(function () use ($commands, $info) {
                foreach ($commands as $i => $command) {
                    $command->execute();
                    $this->stdout($info[$i] . PHP_EOL);
                }
                $this->stdout('Transaction done!' . PHP_EOL);
            });
        }

        $this->stdout('Done!' . PHP_EOL);
    }
}