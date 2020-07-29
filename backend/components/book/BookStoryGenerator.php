<?php

namespace backend\components\book;

use backend\components\book\blocks\Image;
use backend\components\book\blocks\Html;
use backend\components\book\blocks\Link;
use backend\components\book\blocks\Test;
use backend\components\book\blocks\Text;
use backend\components\book\blocks\Transition;
use backend\components\story\AbstractBlock;
use backend\components\story\HTMLBLock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HTMLReader;
use backend\components\story\TestBlock;
use backend\components\story\TextBlock;
use backend\components\story\TransitionBlock;
use common\models\Story;
use common\models\StorySlideBlock;
use Yii;
use yii\db\Query;

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

    private function getStoryLinks()
    {
        $query = (new Query())
            ->select([])
            ->from(StorySlideBlock::tableName())
        ->where('');
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

            $images = [];
            $texts = [];
            $tests = [];
            $transitions = [];
            foreach ($slide->getBlocks() as $block) {

                switch ($block->getType()) {
                    case AbstractBlock::TYPE_TEXT:
                        /** @var $block TextBlock */
                        $texts[] = new Text($block->getText());
                        break;
                    case AbstractBlock::TYPE_IMAGE:
                        /** @var $block ImageBlock */
                        $images[] = new Image($block->getFilePath());
                        break;
                    case AbstractBlock::TYPE_HTML:
                        /** @var $block HTMLBLock */
                        $tests[] = new Html($block->getContent());
                        break;
                    case AbstractBlock::TYPE_TRANSITION:
                        /** @var $block TransitionBlock */
                        $transitions[] = new Transition($block->getText());
                        break;
                    case AbstractBlock::TYPE_TEST:
                        /** @var $block TestBlock */
                        $tests[] = new Test($block->getTestID());
                        break;
                }
            }

            $slideLinks = $this->getSlideLinks($slide->id, $storyLinks);
            $links = [];
            if (count($slideLinks) > 0) {
                foreach ($slideLinks as $link) {
                    $links[] = new Link($link['title'], $link['href']);
                }
            }

            $html .= $this->render('@backend/components/book/views/slide', [
                'images' => $images,
                'texts' => $texts,
                'tests' => $tests,
                'transitions' => $transitions,
                'links' => $links,
            ]);
        }



        return $html;
    }

}