<?php


namespace backend\components\story\writer;


use backend\components\story\ButtonBlock;
use backend\components\story\ImageBlock;
use backend\components\story\layouts\OneColumnLayout;
use backend\components\story\TextBlock;
use backend\components\story\TransitionBlock;
use backend\components\story\writer\HTML\ButtonBlockMarkup;
use backend\components\story\writer\HTML\HeaderBlockMarkup;
use backend\components\story\writer\HTML\ImageBlockMarkup;
use backend\components\story\Slide;
use backend\components\story\writer\HTML\ParagraphBlockMarkup;
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
        $layout = $this->slide->getLayout();
        foreach ($this->slide->getBlocks() as $block) {
            if (get_class($block) === TextBlock::class) {
                if (get_class($layout) === OneColumnLayout::class) {
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
            if (get_class($block) === ImageBlock::class) {
                $html .= (new ImageBlockMarkup($block))->markup();
            }
        }
        $html .= '</section>';
        return $html;
    }

}