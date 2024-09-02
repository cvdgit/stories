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
        if (Yii::$app->user->isGuest) {
            $items[] = [
                'label' => 'Войти',
                'url' => ['/auth/login'],
            ];
        } else {
            $items[] = [
                'label' => '<div class="profile-row dropdown-toggle" data-toggle="dropdown">
                                        <div class="profile-row__image">
                                            ' . Html::img(Yii::$app->user->identity->getProfilePhoto(), ['class' => 'profile-image', 'alt' => 'pic']) . '
                                        </div>
                                    </div>',
                'items' => [
                    ['label' => Yii::$app->user->identity->getProfileName(), 'template' => '<h6 class="dropdown-header">{label}</h6>'],
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
                'options' => ['class' => 'menu-item profile-menu-item'],
                'submenuTemplate' => "\n<div class='user-profile dropdown'>\n<ul class='dropdown-menu dropdown-menu-right'>\n{items}\n</ul>\n</div>\n",
            ];
        }
        return Menu::widget([
            'items' => $items,
            'options' => ['class' => 'navbar-nav align-items-center bg-light p-3 rounded-pill flex-nowrap'],
            'linkTemplate' => Html::a('{label}', '{url}', ['class' => 'text-dark mx-5 menu-item__link']),
            'encodeLabels' => false,
        ]);
    }
}
