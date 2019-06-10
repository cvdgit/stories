<?php

use asu\tagcloud\TagCloud;
use common\models\Tag;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;
use yii\widgets\Menu;
use common\models\Category;

/** @var $this yii\web\View */
/** @var $searchModel common\models\StorySearch */
/** @var $dataProvider yii\data\ActiveDataProvider */
/** @var $action array */

$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
?>
<div class="container">
    <div class="row">
        <nav class="col-sm-4 col-md-3 site-sidebar">
            <?php $form = ActiveForm::begin([
              'action' => $action,
              'method' => 'GET',
              'options' => ['class' => 'story-form'],
            ]); ?>
            <div>
                <?= $form->field($searchModel, 'title', ['inputOptions' => ['class' => 'form-control story-search-control']])
                         ->textInput(['placeholder' => 'Поиск...', 'autocomplete' => 'off'])
                         ->label(false) ?>
                <span class="icon icon-search"></span>
            </div>
            <?php ActiveForm::end(); ?>
            <?php if (!Yii::$app->user->isGuest): ?>
            <h4>Личное</h4>
            <?= Menu::widget([
                'items' => [
                        ['label' => 'История просмотра', 'url' => ['/story/history']],
                ],
                'options' => ['class' => 'story-category-list'],
            ]) ?>
            <?php endif ?>
            <h4>Категории</h4>
            <?= Menu::widget([
              'items' => Category::getCategoriesForMenu(),
              'submenuTemplate' => "\n<ul class=\"story-category-list story-sub-category-list\">\n{items}\n</ul>\n",
              'options' => ['class' => 'story-category-list'],
            ]) ?>
            <h4>Облако тегов</h4>
            <!--noindex-->
            <?= TagCloud::widget([
                'beginColor' => '38405d',
                'endColor' => '000000',
                'minFontSize' => 8,
                'maxFontSize' => 15,
                'displayWeight' => false,
                'tags' => Tag::getPopularTags(),
                'options' => [
                    'style' => 'word-wrap: break-word;'
                ],
            ]) ?>
            <!--/noindex-->
        </nav>
        <main class="col-sm-8 col-md-9 site-main" style="margin-top: 0">
            <h1 style="margin-top: 6px; margin-bottom: 33px"><?= $this->getHeader() ?></h1>
            <?php
            $layout = '<div class="story-list-filter clearfix">
                         {summary}
                       </div>
                       <div class="story-list"><div class="flex-row row">{items}</div></div>
                             <div class="story-pagination">{pager}</div>';
            if (get_class($dataProvider->getSort()) === \frontend\components\StorySorter::class) {
                $order = $dataProvider->getSort()->getCurrentOrderName();
                $layout = '<div class="story-list-filter clearfix">
                               {summary}
                               <div class="pull-right">
                                 <span style="margin-right: 6px">Сортировать по:</span>
                                 <div class="dropdown pull-right" style="cursor: pointer">
                                   <div id="story-sort-dropdown" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">'.$order.' <span class="caret"></span></div>
                                   {sorter}
                                   </div>
                                 </div>
                               </div>
                             </div>
                             <div class="story-list"><div class="flex-row row">{items}</div></div>
                             <div class="story-pagination">{pager}</div>';
            }
            ?>
            <?= ListView::widget([
                'layout' => $layout,
                'summary' => '<span>Показано {count} из {totalCount} историй</span>',
                'dataProvider' => $dataProvider,
                'itemOptions' => ['tag' => false],
                'itemView' => '_storyitem_array',
                'emptyText' => 'Список историй пуст',
                'sorter' => [
                   'options' => [
                        'class' => 'dropdown-menu'
                    ],
                ],
                'pager' => [
                  'options' => [
                    'class' => false,
                  ],
                    'disabledListItemSubTagOptions' => ['tag' => 'a', 'href' => '#'],
                    'disabledPageCssClass' => 'no-pointer',
                    'prevPageCssClass' => false,
                    'nextPageCssClass' => false,
                ],
            ]) ?>
        </main>
    </div>
</div>
