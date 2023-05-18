<?php

declare(strict_types=1);

namespace backend\widgets;

use dosamigos\selectize\SelectizeDropDownList;
use yii\base\Widget;
use yii\web\JsExpression;

class ImportTestWidget extends Widget
{
    public $model;
    public $attribute;
    public $onChange = '{}';
    public $toTestId;

    private $widgetOptions = [];

    public function init(): void
    {
        parent::init();

        $this->widgetOptions = [
            'name' => 'selectTest',
            'id' => $this->id,
            'model' => $this->model,
            'attribute' => $this->attribute,
            'loadUrl' => ['/questions-import/select-test', 'to_test_id' => $this->toTestId],
            'clientOptions' => [
                'valueField' => 'id',
                'labelField' => 'title',
                'searchField' => ['title'],
                'maxItems' => 1,
                'maxOptions' => 30,
                'persist' => false,
                'create' => false,
                'openOnFocus' => true,
                'highlight' => true,
                'scrollDuration' => 60,
                'render' => [
                    'option' => $this->renderOptionExpression(),
                ],
                'onChange' => new JsExpression($this->onChange),
            ],
        ];
    }

    /**
     * @throws \Throwable
     */
    public function run(): string
    {
        return SelectizeDropDownList::widget($this->widgetOptions);
    }

    private function renderOptionExpression(): JsExpression
    {
        return new JsExpression(<<<'JS'
        (item, escape) => `
            <div style="padding:10px">
              <div class="media-body">
                <p class="media-heading">${item.title}</p>
              </div>
            </div>
        `
JS
);
    }
}
