<?php

namespace backend\widgets;

use dosamigos\datepicker\DatePicker;
use yii\widgets\InputWidget;

class WikidsDatePicker extends InputWidget
{

    public $model;
    public $attribute;

    public function run()
    {
        return DatePicker::widget([
            'model' => $this->model,
            'attribute' => $this->attribute,
            'language' => 'ru',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'dd.mm.yyyy'
            ]
        ]);
    }

}