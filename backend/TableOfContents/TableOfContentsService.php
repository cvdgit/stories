<?php

declare(strict_types=1);

namespace backend\TableOfContents;

use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\TableOfContentsBlock;
use backend\components\story\TableOfContentsBlockContent;
use backend\components\story\writer\HTMLWriter;
use common\models\StorySlide;
use modules\edu\query\GetStoryTests\SlideTableOfContents;
use modules\edu\query\GetStoryTests\StoryTestsFetcher;
use Ramsey\Uuid\Uuid;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

class TableOfContentsService
{
    /**
     * @return TableOfContentsItem[]
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function getStoryTableOfContents(int $storyId): array
    {
        $items = (new StoryTestsFetcher())
            ->fetch($storyId)
            ->find(SlideTableOfContents::class);
        return array_map(
            static function (SlideTableOfContents $item): TableOfContentsItem {
                $content = TableOfContentsBlockContent::createFromHtml($item->getContent());
                return new TableOfContentsItem(
                    $item->getSlideId(),
                    Uuid::fromString($content->getId()),
                    TableOfContentsPayload::fromPayload(Json::decode($content->getPayload())),
                );
            },
            $items,
        );
    }

    public function updateTableOfContentsSlide(
        TableOfContentsItem $tableOfContentsItem,
        int $inCardSlideId,
        array $slideIds
    ): void {
        $tableOfContentsPayload = $tableOfContentsItem->getPayload();
        $tableOfContentsPayload->setCardSlides($inCardSlideId, $slideIds);
        $tableOfContentsSlide = StorySlide::findOne($tableOfContentsItem->getSlideId());
        if ($tableOfContentsSlide !== null) {
            $slide = (new HtmlSlideReader($tableOfContentsSlide->getSlideOrLinkData()))->load();
            $blocks = $slide->findBlocksByClassName(TableOfContentsBlock::class);
            if (count($blocks) > 0) {
                $tableOfContentsBlock = $blocks[0];
                $tableOfContentsBlock->setContent(
                    (new TableOfContentsBlockContent(
                        $tableOfContentsItem->getId()->toString(),
                        Json::encode($tableOfContentsPayload),
                    ))->render(),
                );
            }
            $tableOfContentsSlide->updateData(
                (new HTMLWriter())->renderSlide($slide),
            );
        }
    }
}
