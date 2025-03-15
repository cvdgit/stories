<?php

declare(strict_types=1);

namespace backend\SlideEditor\UpdateRetelling;

use backend\components\story\AbstractBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\RetellingBlock;
use backend\components\story\RetellingBlockContent;
use backend\components\story\TextBlock;
use common\models\StorySlide;
use yii\base\Action;
use yii\db\Query;
use yii\web\Request;
use yii\web\Response;

class LoadRetellingAction extends Action
{
    public function run(int $story_id, int $slide_id, string $block_id, Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $slideModel = StorySlide::findOne($slide_id);
        if ($slideModel === null) {
            return ['success' => false, 'message' => 'Slide not found'];
        }

        $slide = (new HtmlSlideReader($slideModel->getSlideOrLinkData()))->load();
        /** @var RetellingBlock $block */
        $block = $slide->findBlockByID($block_id);
        $content = RetellingBlockContent::createFromHtml($block->getContent());

        $retelling = (new Query())
            ->select('*')
            ->from(['t' => 'retelling'])
            ->where(['t.id' => $content->getId()])
            ->one();

        $slideContent = (new Query())
            ->select('t.data')
            ->from(['t' => 'story_slide'])
            ->where(['t.id' => $retelling['slide_id']])
            ->scalar();

        $slide = (new HtmlSlideReader($slideContent))->load();
        $texts = [];
        foreach ($slide->getBlocks() as $slideBlock) {
            if ($slideBlock->typeIs(AbstractBlock::TYPE_TEXT)) {
                /** @var $slideBlock TextBlock */
                $text = $slideBlock->getText();
                if ($text !== '') {
                    $texts[] = strip_tags(trim($text));
                }
            }
        }

        return [
            'success' => true,
            'retelling' => [
                'retellingId' => $content->getId(),
                'texts' => implode("\n", $texts),
                'withQuestions' => $retelling['with_questions'] === '1',
                'questions' => $retelling['questions'],
                'required' => $content->isRequired(),
            ],
        ];
    }
}
