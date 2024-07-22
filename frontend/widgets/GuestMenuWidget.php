<?php

declare(strict_types=1);

namespace frontend\widgets;

use yii\jui\Widget;
use yii\widgets\Menu;

class GuestMenuWidget extends Widget
{
    public function run(): string
    {
        $items = [
            [
                'label' => 'Войти',
                'url' => ['/auth/login'],
            ],
        ];
        return Menu::widget([
            'items' => $items,
            'options' => ['class' => 'navbar-nav align-items-center bg-light p-3 rounded-pill'],
            'linkTemplate' => '<a class="text-dark mx-2 menu-item__link" href="{url}">{label}</a>',
            'encodeLabels' => false,
        ]);
    }
}
