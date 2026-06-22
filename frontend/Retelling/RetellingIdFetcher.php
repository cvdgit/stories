<?php

declare(strict_types=1);

namespace frontend\Retelling;

use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\RetellingBlock;
use backend\components\story\RetellingBlockContent;
use common\models\StorySlide;
use DomainException;

class RetellingIdFetcher
{
    public function fetchBySlideId(int $slideId): string
    {
        $slideModel = StorySlide::findOne($slideId);
        if ($slideModel === null) {
            throw new DomainException('Retelling slide id not found');
        }

        $slide = (new HtmlSlideReader($slideModel->getSlideOrLinkData()))->load();
        $blocks = $slide->findBlocksByClassName(RetellingBlock::class);
        if (count($blocks) === 0) {
            throw new DomainException('Retelling block is not found on slide');
        }
        $block = $blocks[0];
        return RetellingBlockContent::createFromHtml($block->getContent())->getId();
    }
}
