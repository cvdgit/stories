<?php

declare(strict_types=1);

namespace frontend\widgets;

use common\rbac\UserRoles;
use Yii;
use yii\bootstrap\Html;
use yii\jui\Widget;
use yii\widgets\Menu;

class UserMenuWidget extends Widget
{
    public function run(): string
    {
        $itemTemplate = '<a class="dropdown-item menu-item__link" href="{url}">{label}</a>';
        $items = [
            [
                'label' => 'Профиль',
                'url' => ['/profile/index'],
                'template' => $itemTemplate,
            ],
            [
                'label' => 'История просмотра',
                'url' => ['/story/history'],
                'template' => $itemTemplate,
            ],
            [
                'label' => 'Любимые истории',
                'url' => ['/story/liked'],
                'template' => $itemTemplate,
            ],
            [
                'label' => 'Избранные истории',
                'url' => ['/story/favorites'],
                'template' => $itemTemplate,
            ],
            [
                'label' => 'Панель управления',
                'url' => '/admin',
                'visible' => Yii::$app->user->can(UserRoles::PERMISSION_ADMIN_PANEL),
                'template' => $itemTemplate,
            ],
            [
                'label' => Html::beginForm(['/auth/logout']) .
                    Html::submitButton('Выйти', ['class' => 'dropdown-item menu-item__link']) .
                    Html::endForm(),
                'encode' => false,
            ],
        ];
        return Menu::widget([
            'items' => $items,
            'options' => ['class' => 'dropdown-menu dropdown-menu-right'],
            'itemOptions' => ['class' => 'menu-item'],
            'encodeLabels' => false,
        ]);
    }
}
