<?php

namespace backend\widgets;

use common\models\User;
use dosamigos\selectize\SelectizeDropDownList;
use modules\edu\Teacher\ClassBook\TeacherAccess\UserItem;
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

    public $loadUrl = ['/user/autocomplete/select'];
    /** @var array<array-key, UserItem> */
    public $userModels = [];

    private $widgetOptions;
    private $clientOptions = [
        'valueField' => 'id',
        'labelField' => 'title',
        'searchField' => ['title', 'email'],
        'maxItems' => 1,
        'maxOptions' => 30,
        'persist' => false,
        'create' => false,
        'openOnFocus' => true,
        'highlight' => true,
        'scrollDuration' => 60,
        'render' => [],
    ];

    public function init(): void
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
                        'data-data' => $this->getOptionData(
                            $this->userModel->id,
                            $this->userModel->username,
                            $this->userModel->email,
                            '/img/no_avatar.png',
                        ),
                    ],
                ],
            ];
        }

        if ($this->userModel === null && count($this->userModels) > 0) {
            $this->widgetOptions['options']['prompt'] = '';
            $options = [];
            foreach ($this->userModels as $user) {
                $userId = $user->getId();
                $this->widgetOptions['items'][$userId] = $user->getName();
                $options[$userId] = [
                    'data-data' => $this->getOptionData(
                        $userId,
                        $user->getName(),
                        $user->getEmail(),
                        $user->getPhoto(),
                    ),
                ];
            }
            $this->widgetOptions['options']['options'] = $options;
        }

        //$this->clientOptions['onChange'] = new JsExpression($this->onChange);
        $this->clientOptions['render']['option'] = $this->renderOptionExpression();
        $this->widgetOptions['clientOptions'] = $this->clientOptions;
    }

    /**
     * @throws \Throwable
     */
    public function run(): string
    {
        return SelectizeDropDownList::widget($this->widgetOptions);
    }

    private function getOptionData(int $id, string $title, string $email, $cover = ''): string
    {
        return Json::encode([
            'id' => $id,
            'title' => $title,
            'cover' => $cover,
            'email' => $email,
        ]);
    }

    private function renderOptionExpression(): JsExpression
    {
        return new JsExpression(
            /** @lang javascript */ <<<JS
                function(item, escape) {
                    return "<div class=\"media\" style=\"padding:10px\">" +
                             "<div class=\"media-left\">" +
                               "<img alt=\"cover\" height=\"64\" class=\"media-object\" src=\"" + item.cover + "\" />" +
                             "</div>" +
                             "<div class=\"media-body\">" +
                               "<p class=\"media-heading\">" + item.title + "</p>" +
                               "<p class=\"media-heading\">" + item.email + "</p>" +
                             "</div>" +
                           "</div>";
                }
                JS,
        );
    }
}
