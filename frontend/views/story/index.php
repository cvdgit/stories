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

$this->title = 'Каталог историй';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-shop">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-3 col-xs-12 col-3">
                <aside class="sidebar-shop">
                    <div class="widget-search">
                        <h3 class="widget-title">Поиск</h3>
                        <?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get']); ?>
                        <?= $form->field($searchModel, 'title')->textInput(['placeholder' => 'Поиск...'])->label(false) ?>
                        <?= Html::submitButton('<i class="fa fa-search"></i>') ?>
                        <?php ActiveForm::end(); ?>
                    </div>
                    <div class="widget-category">
                        <h3 class="widget-title">Категории</h3>
                        <?php
                        echo Menu::widget([
                            'items' => Category::getCategoriesForMenu(),
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
            ]) ?>
        </div>
    </div>
</div>