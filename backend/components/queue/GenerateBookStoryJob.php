<?php

namespace backend\components\queue;

use backend\services\StoryEditorService;
use common\models\Story;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

class GenerateBookStoryJob extends BaseObject implements JobInterface
{

    /** @var int */
    public $storyID;

    protected $editorService;

    public function __construct(StoryEditorService $editorService, $config = [])
    {
        $this->editorService = $editorService;
        parent::__construct($config);
    }

    /**
     * @param Queue $queue which pushed and is handling the job
     * @return void|mixed result of the job execution
     */
    public function execute($queue)
    {
        $story = Story::findModel($this->storyID);
        $html = $this->editorService->generateBookStoryHtml($story);
        $story->body = $html;
        $story->save(false, ['body']);
    }

}