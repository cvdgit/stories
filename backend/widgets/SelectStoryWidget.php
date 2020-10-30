<?php

namespace backend\widgets;

use common\components\StoryCover;
use common\models\Story;
use dosamigos\selectize\SelectizeTextInput;
use yii\base\Widget;
use yii\web\JsExpression;

class SelectStoryWidget extends Widget
{

    public $loadUrl;
    public $model;
    public $attribute;

    public function init()
    {
        $this->loadUrl = ['story/autocomplite'];
    }

    public function run()
    {
        return SelectizeTextInput::widget([
            'name' => 'selectStory',
            'model' => $this->model,
            'attribute' => $this->attribute,
            //'loadUrl' => $this->loadUrl,
            'options' => [
                'placeholder' => 'Введите название истории',
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
                'render' => [
                    'option' => $this->renderOptionExpression(),
                ],
            ],
        ]);
    }

    private function getOptions()
    {
        return array_map(function(Story $story) {
            return [
                'id' => $story->id,
                'title' => $story->title,
                'cover' => StoryCover::getStoryThumbPath($story->cover),
            ];
        }, Story::findPublishedStories()->all());
    }

    private function renderOptionExpression()
    {
        $js = <<< JS
function(item, escape) {
    return "<div class=\"media\" style=\"padding:10px\">" +
             "<div class=\"media-left\">" +
               "<img alt=\"cover\" height=\"64\" class=\"media-object\" src=\"" + item.cover + "\" />" +
             "</div>" +
             "<div class=\"media-body\">" +
               "<p class=\"media-heading\">" + item.title + "</p>" +
             "</div>" +
           "</div>";
}
JS;
        return new JsExpression($js);
    }

}