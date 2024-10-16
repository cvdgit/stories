<?php

declare(strict_types=1);

namespace backend\components;

use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\SlideView;
use backend\components\story\TextBlock;
use backend\components\story\writer\HTMLWriter;
use yii\base\InvalidConfigException;

class StudyTaskFinalSlide
{
    /**
     * @throws InvalidConfigException
     */
    public static function create(int $slideId): string
    {
        $slide = (new HtmlSlideReader(''))->load();
        $slide->setId($slideId);
        $slide->setView(SlideView::FINAL_SLIDE);

        /** @var TextBlock $textBlock */
        $textBlock = $slide->createBlock(TextBlock::class);
        $textBlock->setSizeAndPosition('290px', 'auto', '495px', '343px');
        $textBlock->setText('<p style="text-align: center;">Задание пройдено</p>');

        $slide->addBlock($textBlock);
        return (new HTMLWriter())->renderSlide($slide);
    }
}
