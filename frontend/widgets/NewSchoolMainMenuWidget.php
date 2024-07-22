<?php

declare(strict_types=1);

namespace frontend\widgets;

use common\rbac\UserRoles;
use modules\edu\components\EduAccessChecker;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\Menu;

class NewSchoolMainMenuWidget extends Widget
{
    private $accessChecker;

    public function __construct(EduAccessChecker $accessChecker, $config = [])
    {
        parent::__construct($config);
        $this->accessChecker = $accessChecker;
    }

    public function run(): string
    {
        $items = [
            [
                'label' => 'Истории для детей',
                'url' => ['/story/index', 'section' => 'stories'],
                'active' => Yii::$app->controller->id === 'story',
            ],
            [
                'label' => 'Блог',
                'url' => ['/news/index'],
                'active' => Yii::$app->controller->id === 'news',
            ],
            [
                'label' => 'Обучение',
                'url' => ['/edu/default/index'],
                'visible' => $this->accessChecker->canUserAccess(Yii::$app->user->getId()),
            ],
            [
                'label' => 'Контакты',
                'url' => '#contact',
                'template'=> '<a class="text-dark mx-5 menu-item__link" href="{url}">{label}</a>',
            ],
        ];
        return Menu::widget([
            'items' => $items,
            'options' => ['class' => 'navbar-nav align-items-center bg-light p-3 rounded-pill flex-wrap'],
            //'itemOptions' => ['class' => 'menu-item'],
            'linkTemplate' => Html::a('{label}', '{url}', ['class' => 'text-dark mx-5 menu-item__link']),
            'encodeLabels' => false,
        ]);
    }
}
