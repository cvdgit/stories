<?php

declare(strict_types=1);

namespace backend\components\book\views;

use yii\bootstrap\Html;

class TwoColumnRenderer
{
    public function render(string $contentOne, string $contentTwo): string
    {
        $contents[] = Html::tag('div', $contentOne, ['class' => 'col-lg-6']);
        $contents[] = Html::tag('div', $contentTwo, ['class' => 'col-lg-6']);
        return Html::tag('div', implode("\n", $contents), ['class' => 'row']);
    }
}
