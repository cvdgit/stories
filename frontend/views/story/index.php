<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;
use yii\widgets\Menu;
use common\models\Category;
use frontend\widgets\StoryLinkSorter;

/* @var $this yii\web\View */
/* @var $searchModel common\models\StorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$title = 'Каталог историй';
$this->setMetaTags($title,
                   $title,
                   'wikids, сказки, истории, каталог историй',
                   $title);
$this->params['breadcrumbs'][] = $title;
?>

<div class="content-shop">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-3 col-xs-12 col-3">
                <aside class="sidebar-shop">
                    <div class="widget-search cst-search">
                        <h3 class="widget-title">Поиск</h3>
                        <?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get']); ?>
                        <?= $form->field($searchModel, 'title', ['inputOptions' => ['class' => null]])->textInput(['placeholder' => 'Поиск...'])->label(false) ?>
                        <?= Html::submitButton('<i class="fa fa-search"></i>', ['class' => null]) ?>
                        <?php ActiveForm::end(); ?>
                    </div>
                    <div class="widget-category">
                        <h3 class="widget-title">Категории</h3>
                        <?php
                        echo Menu::widget([
                            'items' => Category::getCategoriesForMenu(),
                            'submenuTemplate' => "\n<ul class=\"wk-submenu\">\n{items}\n</ul>\n",
                        ]);
                        ?>
                    </div>
                </aside>
            </div>
            <?php
            $css = <<< CSS
.story-sorting-btn {
    padding: 0 15px;
    height: 45px;
    line-height: normal;
    border: none;
    font-size: 18px;
    color: #777;
    background-color: transparent;
    border-radius: 0;
    font-family: 'BrandonRegular', serif;
}
.pagination li a {
    border-radius: 100% !important;
}
.widget-category li {
    margin-bottom: 10px !important;
}
.wk-submenu {
    margin-top: 10px;
}
.wk-submenu li {
    padding-left: 20px;
}
CSS;
            $this->registerCss($css);
            ?>
            <?= ListView::widget([
                'layout' => '<div class="filter-wrap">
                               {summary}
                               <div class="sorting">
                                 <div class="dropdown">
                                   <button class="btn btn-default dropdown-toggle story-sorting-btn" type="button" data-toggle="dropdown">Сортировать <span class="caret"></span></button>
                                   {sorter}
                                 </div>
                               </div>
                               <div class="switch">
                            <span class="list active"><i class="fa fa-list"></i></span>
                            <span class="grid-icon"><i class="fa fa-th"></i></span>
                        </div>
                             </div>
                             <div class="content-product three-column with-sidebar">{items}</div>{pager}',
                'options' => ['class' => 'col-md-9 col-sm-9 col-xs-12 col-9'],
                'summary' => '<p>Показано {count} из {totalCount} историй</p>',
                'dataProvider' => $dataProvider,
                'itemOptions' => ['tag' => false],
                'itemView' => '_storyitem',
                'sorter' => [
                   'options' => [
                        'class' => 'dropdown-menu'
                    ],
                    'attributes' => [
                        'title',
                        'created_at',
                    ]
                ],
                'pager' => [
                    'options' => [
                        'class' => 'pagination border-top border-color-gray',
                    ],
                    'disabledListItemSubTagOptions' => ['tag' => 'a', 'href' => '#'],
                    'disabledPageCssClass' => 'no-pointer',
                    'prevPageCssClass' => false,
                    'nextPageCssClass' => false,
                ],
            ]) ?>
        </div>
    </div>
</div>