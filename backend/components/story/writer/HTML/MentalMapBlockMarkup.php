<?php

declare(strict_types=1);

namespace backend\components\story\writer\HTML;

use backend\components\story\MentalMapBlock;
use backend\components\story\StyleHelper;
use yii\helpers\Html;

class MentalMapBlockMarkup implements BlockMarkupInterface
{
    private $block;
    public function __construct(MentalMapBlock $block)
    {
        $this->block = $block;
    }

    public function markup(): string
    {
        $elementTag = $this->block->getContent();
        $contentBlockTag = Html::tag('div', $elementTag, [
            'class' => 'sl-block-content',
            'style' => StyleHelper::arrayToStyle([
                'z-index' => 11,
            ]),
        ]);
        return Html::tag('div', $contentBlockTag, [
            'class' => 'sl-block',
            'data-block-id' => $this->block->getId(),
            'data-block-type' => 'mental_map',
            'style' => StyleHelper::arrayToStyle([
                'min-width' => '4px',
                'min-height' => '4px',
                'width' => $this->block->getWidth(),
                'height' => $this->block->getHeight(),
                'left' => $this->block->getLeft(),
                'top' => $this->block->getTop(),
            ]),
        ]);
    }
}
