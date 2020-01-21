<?php

namespace backend\components\queue;

use backend\services\ImageService;
use backend\services\StoryEditorService;
use common\models\Story;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

class GenerateBookStoryJob extends BaseObject implements JobInterface
{

    /** @var int */
    public $storyID;

    /**
     * @param Queue $queue which pushed and is handling the job
     * @return void|mixed result of the job execution
     */
    public function execute($queue)
    {
        $story = Story::findModel($this->storyID);
        $editorService = new StoryEditorService(new ImageService());
        $html = $editorService->generateBookStoryHtml($story);
        $story->body = $html;
        $story->save(false, ['body']);
    }

}