<?php

namespace backend\components\book;

use backend\components\BlockRenderer;
use backend\components\book\blocks\Image;
use backend\components\book\blocks\Link;
use backend\components\book\blocks\Test;
use backend\components\book\blocks\Text;
use backend\components\book\blocks\Video;
use backend\components\book\views\OneColumnRenderer;
use backend\components\book\views\TwoColumnRenderer;
use backend\components\story\AbstractBlock;
use backend\components\story\HTMLBLock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HTMLReader;
use backend\components\story\TestBlock;
use backend\components\story\TextBlock;
use backend\components\story\TransitionBlock;
use backend\components\story\VideoBlock;
use common\models\Story;
use common\models\StoryTest;
use Yii;
use yii\web\View;

class BookStoryGenerator
{
    private $view;

    private function getView(): View
    {
        if ($this->view === null) {
            $this->view = Yii::$app->getView();
        }
        return $this->view;
    }

    private function render($view, $params = []): string
    {
        return $this->getView()->render($view, $params, $this);
    }

    private function getSlideLinks($slideID, $storyLinks): array
    {
        return array_filter($storyLinks, static function($value) use ($slideID) {
            return (int) $slideID === (int) $value['slideID'];
        });
    }

    public function generate(Story $model): string
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
                        $slideBlocks->addGuestBlock(new Text($block->getText()));
                        break;
                    case AbstractBlock::TYPE_IMAGE:
                        /** @var $block ImageBlock */
                        $slideBlocks->addGuestBlock(new Image($block->getFilePath()));
                        break;
                    case AbstractBlock::TYPE_HTML:
                        /** @var $block HTMLBLock */
                        $content = $block->getContent();
                        $fragment = \phpQuery::newDocumentHTML($content);
                        $testId = $fragment->find('.new-questions')->attr('data-test-id');
                        if ($testId !== null) {
                            $test = StoryTest::findOne($testId);
                            if ($test !== null) {
                                $slideBlocks->addGuestBlock(new Test($testId, $test->header, $test->description_text));
                            }
                        }
                        break;
                    //case AbstractBlock::TYPE_TRANSITION:
                        /** @var $block TransitionBlock */
                    //    $slideBlocks->createTransitions($block->getText());
                    //    break;
                    case AbstractBlock::TYPE_TEST:
                        /** @var $block TestBlock */
                        $testId = $block->getTestID();
                        if ($testId !== null) {
                            $test = StoryTest::findOne($testId);
                            if ($test !== null) {
                                $slideBlocks->addGuestBlock(new Test($testId, $test->header, $test->description_text));
                            }
                        }
                        break;
                    case AbstractBlock::TYPE_VIDEO:
                        /** @var $block VideoBlock */
                        $slideBlocks->addGuestBlock(new Video($block->getVideoId()));
                        break;
                }
            }

            $slideLinks = $this->getSlideLinks($slide->id, $storyLinks);
            if (count($slideLinks) > 0) {
                foreach ($slideLinks as $link) {
                    $slideBlocks->addGuestBlock(new Link($link['title'], $link['href']));
                }
            }
            if ($slideBlocks->isEmpty()) {
                continue;
            }

            $html .= $this->render('@backend/components/book/views/slide', [
                'content' => $this->renderSlide($slideBlocks),
            ]);
        }
        return $html;
    }

    private function renderSlide(SlideBlocks $slideBlocks): string
    {
        $content = '';
        if (!$slideBlocks->getGuestBlocks(Video::class)->isEmpty()) {
            $content = (new TwoColumnRenderer())
                ->render(
                    BlockRenderer::renderVideos($slideBlocks->getGuestBlocks(Video::class)),
                    BlockRenderer::renderTexts($slideBlocks->getGuestBlocks(Text::class))
                );
        } else {
            if (!$slideBlocks->getGuestBlocks(Image::class)->isEmpty() && !$slideBlocks->getGuestBlocks(Text::class)->isEmpty()) {
                $content = (new TwoColumnRenderer())
                    ->render(
                        BlockRenderer::renderImages($slideBlocks->getGuestBlocks(Image::class)),
                        BlockRenderer::renderTexts($slideBlocks->getGuestBlocks(Text::class))
                    );
            } else {
                if ($slideBlocks->getGuestBlocks(Text::class)->isEmpty() && !$slideBlocks->getGuestBlocks(Image::class)->isEmpty()) {
                    $content = (new OneColumnRenderer())
                        ->render(
                            BlockRenderer::renderImages($slideBlocks->getGuestBlocks(Image::class))
                        );
                }
                if ($slideBlocks->getGuestBlocks(Image::class)->isEmpty() && !$slideBlocks->getGuestBlocks(Text::class)->isEmpty()) {
                    $content = (new OneColumnRenderer())
                        ->render(
                            BlockRenderer::renderTexts($slideBlocks->getGuestBlocks(Text::class))
                        );
                }
            }
        }

        if (!$slideBlocks->getGuestBlocks(Test::class)->isEmpty()) {
            $content = (new OneColumnRenderer())
                ->render(
                    BlockRenderer::renderTests($slideBlocks->getGuestBlocks(Test::class))
                );
        }

        if (!$slideBlocks->getGuestBlocks(Link::class)->isEmpty()) {
            $content .= "\n" . (new OneColumnRenderer(['class' => 'row guest-story-links'], ['class' => 'col-lg-10 col-lg-offset-1 text-center']))
                ->render(
                    BlockRenderer::renderLinks($slideBlocks->getGuestBlocks(Link::class))
                );
        }

        return $content;
    }
}
