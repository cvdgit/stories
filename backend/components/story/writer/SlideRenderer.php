<?php


namespace backend\components\story\writer;


use backend\components\story\AbstractBlock;
use backend\components\story\ButtonBlock;
use backend\components\story\ImageBlock;
use backend\components\story\TestBlock;
use backend\components\story\TextBlock;
use backend\components\story\TransitionBlock;
use backend\components\story\writer\HTML\ButtonBlockMarkup;
use backend\components\story\writer\HTML\HeaderBlockMarkup;
use backend\components\story\writer\HTML\ImageBlockMarkup;
use backend\components\story\Slide;
use backend\components\story\writer\HTML\ParagraphBlockMarkup;
use backend\components\story\writer\HTML\TestBlockMarkup;
use backend\components\story\writer\HTML\TransitionBlockMarkup;

class SlideRenderer
{

    protected $slide;

    public function __construct(Slide $slide)
    {
        $this->slide = $slide;
    }

    public function render(): string
    {
        $html = '<section data-id="" data-background-color="#000000">';
        foreach ($this->slide->getBlocks() as $block) {
            if (get_class($block) === TextBlock::class) {
                if ($block->getType() === AbstractBlock::TYPE_HEADER) {
                    $html .= (new HeaderBlockMarkup($block))->markup();
                }
                else {
                    $html .= (new ParagraphBlockMarkup($block))->markup();
                }
            }
            if (get_class($block) === ButtonBlock::class) {
                $html .= (new ButtonBlockMarkup($block))->markup();
            }
            if (get_class($block) === TransitionBlock::class) {
                $html .= (new TransitionBlockMarkup($block))->markup();
            }
            if (get_class($block) === TestBlock::class) {
                $html .= (new TestBlockMarkup($block))->markup();
            }
            if (get_class($block) === ImageBlock::class) {
                $html .= (new ImageBlockMarkup($block))->markup();
            }
        }
        $html .= '</section>';
        return $html;
    }

}