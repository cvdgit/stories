<?php

namespace modules\edu\widgets\grid;

use yii\bootstrap\Html;
use yii\grid\DataColumn;

class ArrowColumn extends DataColumn
{

    public $url;

    protected function renderDataCellContent($model, $key, $index): string
    {
        return Html::a('<i class="glyphicon glyphicon-chevron-right"></i>', call_user_func($this->url, $model), ['data-pjax' => 0]);
    }
}
