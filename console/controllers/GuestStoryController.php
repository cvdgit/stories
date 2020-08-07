<?php

namespace console\controllers;

use backend\components\book\BookStoryGenerator;
use common\models\Story;
use common\models\StorySlide;
use Yii;
use yii\console\Controller;
use yii\db\Query;

class GuestStoryController extends Controller
{

    protected $bookStoryGenerator;

    public function __construct($id, $module, BookStoryGenerator $bookStoryGenerator, $config = [])
    {
        $this->bookStoryGenerator = $bookStoryGenerator;
        parent::__construct($id, $module, $config);
    }

    public function actionGenerate()
    {
        $time = time() - (60 * 30);

        $query = (new Query())
            ->select(['id AS storyID'])
            ->from(Story::tableName())
            ->where('updated_at > :time', [':time' => $time]);

        $query2 = (new Query())
            ->select(['story_id AS storyID'])
            ->distinct()
            ->from(StorySlide::tableName())
            ->where('updated_at > :time', [':time' => $time]);

        $query->union($query2);

        $storyIDs = [];
        foreach ($query->each() as $row) {
            $storyIDs[] = $row['storyID'];
        }

        if (count($storyIDs) > 0) {
            $models = Story::find()->where(['in', 'id', $storyIDs])->published()->all();
            foreach ($models as $model) {
                //$this->stdout($model->title . PHP_EOL);
                $model->body = $this->bookStoryGenerator->generate($model);
                $model->save(false, ['body']);
            }
        }
        //$this->stdout('Done!' . PHP_EOL);
    }

    public function actionReset()
    {
        $command = Yii::$app->db->createCommand();
        $command->update(Story::tableName(), ['body' => null], 'status = :status', [':status' => Story::STATUS_PUBLISHED]);
        $affectedRows = $command->execute();
        $this->stdout($affectedRows . ' Done!' . PHP_EOL);
    }

    public function actionGenerateAll()
    {
        $query = (new Query())
            ->select(['id AS storyID'])
            ->from(Story::tableName())
            ->where('body IS NULL')
            ->andWhere('status = :status', [':status' => Story::STATUS_PUBLISHED]);
        $storyIDs = [];
        foreach ($query->each() as $row) {
            $storyIDs[] = $row['storyID'];
        }
        if (count($storyIDs) > 0) {
            $models = Story::find()->where(['in', 'id', $storyIDs])->published()->all();
            foreach ($models as $model) {

                $this->stdout($model->title . PHP_EOL);
                $model->body = $this->bookStoryGenerator->generate($model);
                $model->save(false, ['body']);
            }
        }
    }

}