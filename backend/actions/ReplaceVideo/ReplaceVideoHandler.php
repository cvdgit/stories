<?php

declare(strict_types=1);

namespace backend\actions\ReplaceVideo;

use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\writer\HTMLWriter;
use backend\models\video\VideoSource;
use common\models\Story;
use yii\web\NotFoundHttpException;

class ReplaceVideoHandler
{
    private $htmlWriter;

    public function __construct(HTMLWriter $htmlWriter)
    {
        $this->htmlWriter = $htmlWriter;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function handle(ReplaceVideoForm $command): void
    {
        $storyModel = Story::findOne($command->story_id);
        if ($storyModel === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        foreach ($storyModel->storySlides as $storySlide) {
            $slide = (new HtmlSlideReader($storySlide->data))->load();
            $slideChanged = false;
            foreach ($slide->getVideoBlocks() as $block) {
                if (($block->getSource() === VideoSource::YOUTUBE) && in_array($block->getVideoId(), $command->videos, true)) {
                    $block->setSource(VideoSource::FILE);
                    $block->setVideoId($command->replace_video_id);
                    $slideChanged = true;
                }
            }

            if ($slideChanged) {
                \Yii::$app->db->createCommand()
                    ->update('story_slide', [
                        'data' => $this->htmlWriter->renderSlide($slide),
                        'updated_at' => time(),
                    ], ['id' => $storySlide->id])
                    ->execute();
            }
        }
    }
}
