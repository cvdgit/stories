<?php

namespace frontend\widgets;

use common\models\SiteSection;
use modules\edu\components\EduAccessChecker;
use Yii;
use yii\jui\Widget;
use yii\widgets\Menu;

class MainMenuWidget extends Widget
{

    private $accessChecker;

    public function __construct(EduAccessChecker $accessChecker, $config = [])
    {
        parent::__construct($config);
        $this->accessChecker = $accessChecker;
    }

    public function run()
    {
        $sectionMenuItems = SiteSection::allVisibleForMenu(Yii::$app->request->get('section'));
        $menuItems = [
/*            [
                'label' => '<span>Разделы <b class="caret"></b></span>',
                'items' => $sectionMenuItems,
                'options' => ['class' => 'sub-dropdown'],
            ],*/
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
                'active' => Yii::$app->controller->module->id === 'edu',
            ],
            [
                'label' => 'Контакты',
                'url' => '#',
                'template'=> '<a href="{url}" data-toggle="modal" data-target="#wikids-feedback-modal">{label}</a>',
            ],
        ];
        return Menu::widget([
            'encodeLabels' => false,
            'items' => $menuItems,
            'options' => ['class' => 'site-menu site-main-menu horizontal-nav collapse'],
            'submenuTemplate' => "\n<ul class='sub-dropdown-content'>\n{items}\n</ul>\n",
        ]);
    }
}
