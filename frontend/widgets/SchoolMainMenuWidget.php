<?php

declare(strict_types=1);

namespace frontend\widgets;

use common\rbac\UserRoles;
use modules\edu\components\EduAccessChecker;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\Menu;

class SchoolMainMenuWidget extends Widget
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
                'label' => '<span class="menu-item__link dropdown-toggle">Разделы</span>',
                'items' => [
                    ['label' => 'Истории для детей', 'url' => ['/story/index', 'section' => 'stories']],
                    ['label' => 'DIRECTUM', 'url' => ['/story/index', 'section' => 'directum']]
                ],
                'options' => ['class' => 'menu-item dropdown'],
                'submenuTemplate' => "\n<ul class='dropdown-menu'>\n{items}\n</ul>\n",
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
                'url' => '#',
                'template'=> '<a class="menu-item__link" href="{url}" data-toggle="modal" data-target="#wikids-feedback-modal">{label}</a>',
            ],
        ];
        if (Yii::$app->user->isGuest) {
            $items[] = [
                'label' => 'Войти',
                'url' => ['/auth/login'],
                'linkOptions' => ['class' => 'menu-item__link'],
            ];
            $items[] = [
                'label' => 'Регистрация',
                'url' => ['/signup/request'],
                'template' => Html::a('{label}', '{url}', ['class' => 'registration-link', 'onclick' => "ym(53566996, 'reachGoal', 'show_registration_form'); return true;"]),
            ];
        }
        else {
            $items[] = [
                'label' => '<div class="profile-row dropdown-toggle" data-toggle="dropdown">
                                        <div class="profile-row__name">' . Yii::$app->user->identity->getProfileName() . '</div>
                                        <div class="profile-row__image">
                                            ' . Html::img(Yii::$app->user->identity->getProfilePhoto(), ['class' => 'profile-image', 'alt' => 'pic']) . '
                                        </div>
                                    </div>',
                'items' => [
                    ['label' => 'Профиль', 'url' => ['/profile/index'], 'template' => Html::a('{label}', '{url}', ['class' => 'dropdown-item menu-item__link'])],
                    ['label' => 'История просмотра', 'url' => ['/story/history'], 'template' => Html::a('{label}', '{url}', ['class' => 'dropdown-item menu-item__link'])],
                    ['label' => 'Любимые истории', 'url' => ['/story/liked'], 'template' => Html::a('{label}', '{url}', ['class' => 'dropdown-item menu-item__link'])],
                    ['label' => 'Избранные истории', 'url' => ['/story/favorites'], 'template' => Html::a('{label}', '{url}', ['class' => 'dropdown-item menu-item__link'])],
                    ['label' => 'Панель управления', 'url' => '/admin', 'visible' => Yii::$app->user->can(UserRoles::PERMISSION_ADMIN_PANEL), 'template' => Html::a('{label}', '{url}', ['class' => 'dropdown-item menu-item__link'])],
                    ['label' => Html::beginForm(['/auth/logout']) .
                        Html::submitButton('Выйти', ['class' => 'dropdown-item menu-item__link']) .
                        Html::endForm(),
                        'encode' => false,
                    ],
                ],
                'options' => ['class' => 'menu-item'],
                'submenuTemplate' => "\n<div class='user-profile dropdown'>\n<ul class='dropdown-menu dropdown-menu-right'>\n{items}\n</ul>\n</div>\n",
            ];
        }
        return Menu::widget([
            'items' => $items,
            'options' => ['class' => 'navbar-nav align-items-center'],
            'itemOptions' => ['class' => 'menu-item'],
            'linkTemplate' => Html::a('{label}', '{url}', ['class' => 'menu-item__link']),
            'encodeLabels' => false,
        ]);
    }
}
