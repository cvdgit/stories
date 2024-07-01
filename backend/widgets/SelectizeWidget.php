<?php

namespace backend\widgets;

use dosamigos\selectize\SelectizeTextInput;
use yii\base\Widget;
use yii\web\JsExpression;

class SelectizeWidget extends Widget
{

    public $model;
    public $attribute;

    public $options = [];
    public $onChange = '() => {}';

    public function run()
    {
        $this->options = [
            'name' => 'name',
            'model' => $this->model,
            'attribute' => $this->attribute,
            'options' => [
                'placeholder' => 'Введите значение',
            ],
            'clientOptions' => [
                'valueField' => 'id',
                'labelField' => 'title',
                'searchField' => ['title'],
                'maxItems' => 1,
                'persist' => false,
                'create' => false,
                'allowEmptyOption' => true,
                'options' => $this->getOptions(),
                'items' => [$this->model->{$this->attribute}],
                'onChange' => new JsExpression($this->onChange),
                //'render' => [
                //    'option' => $this->renderOptionExpression(),
                //],
            ],
        ];
        return SelectizeTextInput::widget($this->options);
    }

    protected function getOptions()
    {
        return [];
    }

}
