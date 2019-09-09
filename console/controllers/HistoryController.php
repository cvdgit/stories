<?php


namespace console\controllers;


use Yii;
use yii\console\Controller;

class HistoryController extends Controller
{

    public function actionClear()
    {
        $command = Yii::$app->db->createCommand();
        $command->update('{{%story}}', ['views_number' => 0]);
        $command->execute();

        $command = Yii::$app->db->createCommand();
        $command->truncateTable('{{%story_statistics}}');
        $command->execute();

        $this->stdout('Done!' . PHP_EOL);
    }

}