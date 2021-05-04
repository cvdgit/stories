<?php

namespace backend\widgets;

use common\models\TestWordList;
use dosamigos\selectize\SelectizeTextInput;
use yii\base\Widget;

class SelectWordListWidget extends Widget
{

    public $model;
    public $attribute;

    public function run()
    {
        return SelectizeTextInput::widget([
            'name' => 'selectStory',
            'model' => $this->model,
            'attribute' => $this->attribute,
            //'loadUrl' => $this->loadUrl,
            'options' => [
                'placeholder' => 'Введите название',
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
                //'render' => [
                //    'option' => $this->renderOptionExpression(),
                //],
            ],
        ]);
    }

    private function getOptions()
    {
        return array_map(function(TestWordList $model) {
            return [
                'id' => $model->id,
                'title' => $model->name,
            ];
        }, TestWordList::find()->all());
    }

}