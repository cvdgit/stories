<?php

namespace frontend\widgets;

use common\helpers\Url;
use common\models\SiteSection;
use Yii;
use yii\jui\Widget;
use yii\widgets\Menu;

class MainMenuWidget extends Widget
{

    public function run()
    {
        $sectionMenuItems = SiteSection::allVisibleForMenu(Yii::$app->request->get('section'));
        $menuItems = [
            //['label' => 'Главная', 'url' => Url::homeRoute()],
            ['label' => '<span>Разделы <b class="caret"></b></span>', 'items' => $sectionMenuItems, 'options' => ['class' => 'sub-dropdown']],
            ['label' => 'Блог', 'url' => ['news/index'], 'active' => Yii::$app->controller->id === 'news'],
            //['label' => 'Подписки', 'url' => ['/rate/index']],
            ['label' => 'Обучение', 'url' => ['/edu/default/index']],
            ['label' => 'Контакты', 'url' => '#', 'template'=> '<a href="{url}" data-toggle="modal" data-target="#wikids-feedback-modal">{label}</a>'],
        ];
        return Menu::widget([
            'encodeLabels' => false,
            'items' => $menuItems,
            'options' => ['class' => 'site-menu site-main-menu horizontal-nav collapse'],
            'submenuTemplate' => "\n<ul class='sub-dropdown-content'>\n{items}\n</ul>\n",
        ]);
    }
}
