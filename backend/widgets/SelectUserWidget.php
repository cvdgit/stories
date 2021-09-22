<?php

namespace backend\widgets;

use common\models\User;
use dosamigos\selectize\SelectizeDropDownList;
use yii\base\Widget;
use yii\helpers\Json;
use yii\web\JsExpression;

class SelectUserWidget extends Widget
{

    /** @var User */
    public $userModel;
    public $model;
    public $attribute;

    public $onChange = '{}';

    public $loadUrl = ['user/autocomplete/select'];

    private $widgetOptions;
    private $clientOptions = [
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
        'render' => [],
    ];

    public function init()
    {
        $this->widgetOptions = [
            'name' => 'selectUser',
            'id' => $this->id,
            'model' => $this->model,
            'attribute' => $this->attribute,
            'loadUrl' => $this->loadUrl,
        ];
        if ($this->userModel !== null) {
            $this->widgetOptions['items'] = [$this->userModel->id => $this->userModel->username];
            $this->widgetOptions['options'] = [
                'options' => [
                    $this->userModel->id => [
                        'data-data' => $this->getOptionData($this->userModel->id, $this->userModel->username, '/img/no_avatar.png'),
                    ],
                ],
            ];
        }

        //$this->clientOptions['onChange'] = new JsExpression($this->onChange);
        $this->clientOptions['render']['option'] = $this->renderOptionExpression();
        $this->widgetOptions['clientOptions'] = $this->clientOptions;
    }

    public function run()
    {
        return SelectizeDropDownList::widget($this->widgetOptions);
    }

    private function getOptionData(int $id, string $title, $cover = ''): string
    {
        return Json::encode([
            'id' => $id,
            'title' => $title,
            'cover' => $cover,
        ]);
    }

    private function renderOptionExpression(): JsExpression
    {
        return new JsExpression(<<<JS
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
JS
        );
    }
}
