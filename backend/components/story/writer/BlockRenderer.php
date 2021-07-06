<?php

namespace backend\components\story\writer;

use backend\components\story\AbstractBlock;
use backend\components\story\ButtonBlock;
use backend\components\story\HTMLBLock;
use backend\components\story\ImageBlock;
use backend\components\story\TestBlock;
use backend\components\story\TextBlock;
use backend\components\story\TransitionBlock;
use backend\components\story\VideoBlock;
use backend\components\story\VideoFileBlock;
use backend\components\story\writer\HTML\ButtonBlockMarkup;
use backend\components\story\writer\HTML\HeaderBlockMarkup;
use backend\components\story\writer\HTML\HTMLBlockMarkup;
use backend\components\story\writer\HTML\ImageBlockMarkup;
use backend\components\story\writer\HTML\ParagraphBlockMarkup;
use backend\components\story\writer\HTML\TestBlockMarkup;
use backend\components\story\writer\HTML\TransitionBlockMarkup;
use backend\components\story\writer\HTML\VideoBlockMarkup;

class BlockRenderer
{

    public function render(AbstractBlock $block): string
    {
        $markupClassName = '';
        switch (get_class($block)) {
            case TextBlock::class:
                if ($block->getType() === AbstractBlock::TYPE_HEADER) {
                    $markupClassName = HeaderBlockMarkup::class;
                }
                else {
                    $markupClassName = ParagraphBlockMarkup::class;
                }
            break;
            case ButtonBlock::class:
                $markupClassName = ButtonBlockMarkup::class;
                break;
            case TransitionBlock::class:
                $markupClassName = TransitionBlockMarkup::class;
                break;
            case TestBlock::class:
                $markupClassName = TestBlockMarkup::class;
                break;
            case ImageBlock::class:
                $markupClassName = ImageBlockMarkup::class;
                break;
            case HTMLBLock::class:
                $markupClassName = HTMLBlockMarkup::class;
                break;
            case VideoBlock::class:
            case VideoFileBlock::class:
            $markupClassName = VideoBlockMarkup::class;
                break;
        }
        return (new $markupClassName($block))->markup();
    }

}