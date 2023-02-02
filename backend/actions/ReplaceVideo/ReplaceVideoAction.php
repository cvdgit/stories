<?php

declare(strict_types=1);

namespace backend\actions\ReplaceVideo;

use backend\components\story\reader\HtmlSlideReader;
use backend\models\video\VideoSource;
use common\models\SlideVideo;
use common\models\Story;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class ReplaceVideoAction extends Action
{
    /** @var ReplaceVideoHandler */
    private $handler;

    public function __construct($id, $controller, ReplaceVideoHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    /**
     * @throws NotFoundHttpException
     * @return array|string
     */
    public function run(int $story_id, Request $request, Response $response)
    {
        $storyModel = Story::findOne($story_id);
        if ($storyModel === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $videos = [];
        foreach ($storyModel->storySlides as $storySlide) {
            $slide = (new HtmlSlideReader($storySlide->data))->load();
            foreach ($slide->getVideoBlocks() as $block) {
                if ($block->getSource() === VideoSource::YOUTUBE) {
                    $videoModel = SlideVideo::findModelByVideoID($block->getVideoId());
                    if ($videoModel !== null) {
                        $videos[$block->getVideoId()] = new VideoDto(
                            $videoModel->id,
                            $block->getSource(),
                            $videoModel->title,
                            $block->getVideoId()
                        );
                    }
                }
            }
        }

        $replaceForm = new ReplaceVideoForm();
        $replaceForm->story_id = $storyModel->id;
        $replaceForm->videos = array_map(static function(VideoDto $video) {
            return $video->getVideoId();
        }, $videos);

        if ($replaceForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;

            try {
                $this->handler->handle($replaceForm);
                return ['success' => true, 'message' => 'Успешно'];
            } catch (\Exception $exception) {
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }

        $videoItems = SlideVideo::videoFileArray();

        return $this->controller->renderAjax('replace', [
            'storyVideos' => $videos,
            'formModel' => $replaceForm,
            'videoItems' => $videoItems,
        ]);
    }
}
