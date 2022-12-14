<?php

declare(strict_types=1);

namespace backend\components\book\views;

use yii\bootstrap\Html;

class OneColumnRenderer
{
    private $rowOptions = [];
    private $colOptions = [];

    public function __construct(array $rowOptions = [], array $colOptions = [])
    {
        $this->colOptions = array_merge(['class' => 'col-lg-10 col-lg-offset-1'], $colOptions);
        $this->rowOptions = array_merge(['class' => 'row'], $rowOptions);
    }

    public function render(string $content): string
    {
        $contents[] = Html::tag('div', $content, $this->colOptions);
        return Html::tag('div', implode("\n", $contents), $this->rowOptions);
    }
}
