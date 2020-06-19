<?php


namespace backend\components\queue;


use backend\components\story\AbstractBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\writer\HTMLWriter;
use common\models\Story;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

class ChangeVideoJob extends BaseObject implements JobInterface
{

    /** @var string */
    public $oldVideoID;

    /** @var string */
    public $newVideoID;

    /**
     * @param Queue $queue which pushed and is handling the job
     * @return void|mixed result of the job execution
     */
    public function execute($queue)
    {
        $models = Story::find()->with('storySlides')->andWhere(['video' => 1])->all();
        foreach ($models as $model) {
            foreach ($model->storySlides as $slideModel) {
                $slideChanged = false;
                $reader = new HtmlSlideReader($slideModel->data);
                $slide = $reader->load();
                foreach ($slide->getBlocks() as $block) {
                    if (($block->getType() === AbstractBlock::TYPE_VIDEO) && $block->getVideoId() === $this->oldVideoID) {
                        $block->setVideoId($this->newVideoID);
                        $slideChanged = true;
                    }
                }
                if ($slideChanged) {
                    $writer = new HTMLWriter();
                    $slideModel->data = $writer->renderSlide($slide);
                    $slideModel->save(false, ['data']);
                }
            }
        }
    }

}