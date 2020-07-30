<?php

namespace console\controllers;

use backend\components\book\BookStoryGenerator;
use common\models\Story;
use common\models\StorySlide;
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

        foreach ($query->each() as $row) {
            $this->stdout($row['storyID'] . PHP_EOL);
        }
    }

}