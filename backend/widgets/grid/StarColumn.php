<?php

namespace backend\widgets\grid;

use yii\grid\DataColumn;
use yii\helpers\Html;

class StarColumn extends DataColumn
{

    protected function renderDataCellContent($model, $key, $index): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $stars .= $this->createStar($i, $model->correct_answers);
        }
        return $stars;
    }

    private function createStar(int $index, int $current)
    {
        $className = 'star-empty';
        if ($index <= $current) {
            $className = 'star';
        }
        return Html::tag('i', '', ['class' => 'glyphicon glyphicon-' . $className]);
    }

}