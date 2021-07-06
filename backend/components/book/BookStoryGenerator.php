<?php

namespace backend\components\book;

use backend\components\story\AbstractBlock;
use backend\components\story\HTMLBLock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HTMLReader;
use backend\components\story\TestBlock;
use backend\components\story\TextBlock;
use backend\components\story\TransitionBlock;
use backend\components\story\VideoBlock;
use common\models\Story;
use Yii;

class BookStoryGenerator
{

    private $view;

    protected function getView()
    {
        if ($this->view === null) {
            $this->view = Yii::$app->getView();
        }
        return $this->view;
    }

    protected function render($view, $params = [])
    {
        return $this->getView()->render($view, $params, $this);
    }

    private function getSlideLinks($slideID, $storyLinks)
    {
        return array_filter($storyLinks, function($value) use ($slideID) {
            return (int) $slideID === (int) $value['slideID'];
        });
    }

    public function generate(Story $model)
    {
        $storyLinks = $model->slideBlocksData();
        $story = (new HTMLReader($model->slidesData()))->load();
        $html = '';
        foreach ($story->getSlides() as $slide) {
            $slideBlocks = new SlideBlocks();
            foreach ($slide->getBlocks() as $block) {
                switch ($block->getType()) {
                    case AbstractBlock::TYPE_TEXT:
                        /** @var $block TextBlock */
                        $slideBlocks->createTexts($block->getText());
                        break;
                    case AbstractBlock::TYPE_IMAGE:
                        /** @var $block ImageBlock */
                        $slideBlocks->createImages($block->getFilePath());
                        break;
                    case AbstractBlock::TYPE_HTML:
                        /** @var $block HTMLBLock */
                        $slideBlocks->createHtmlTests($block->getContent());
                        break;
                    case AbstractBlock::TYPE_TRANSITION:
                        /** @var $block TransitionBlock */
                        $slideBlocks->createTransitions($block->getText());
                        break;
                    case AbstractBlock::TYPE_TEST:
                        /** @var $block TestBlock */
                        $slideBlocks->createTests($block->getTestID());
                        break;
                    case AbstractBlock::TYPE_VIDEO:
                        /** @var $block VideoBlock */
                        $slideBlocks->createVideos($block->getVideoId());
                }
            }
            $slideLinks = $this->getSlideLinks($slide->id, $storyLinks);
            if (count($slideLinks) > 0) {
                foreach ($slideLinks as $link) {
                    $slideBlocks->createLinks($link['title'], $link['href']);
                }
            }
            if ($slideBlocks->isEmpty()) {
                continue;
            }
            $html .= $this->render('@backend/components/book/views/slide', ['manager' => $slideBlocks]);
        }
        return $html;
    }
}
