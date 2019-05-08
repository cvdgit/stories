<?php

use yii\widgets\ListView;
use yii\widgets\ActiveForm;
use yii\widgets\Menu;
use common\models\Category;

/** @var $this yii\web\View */
/** @var $searchModel common\models\StorySearch */
/** @var $dataProvider yii\data\ActiveDataProvider */
/** @var $h1 string */
/** @var $action array */

$title = 'Каталог историй';
$this->setMetaTags($title,
                   $title,
                   'wikids, сказки, истории, каталог историй',
                   $title);
$this->params['breadcrumbs'][] = $title;
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
    <h4>Каталог историй</h4>
    <?= Menu::widget([
      'items' => Category::getCategoriesForMenu(),
      'submenuTemplate' => "\n<ul class=\"story-category-list story-sub-category-list\">\n{items}\n</ul>\n",
      'options' => ['class' => 'story-category-list'],
    ]) ?>
  </nav>
  <main class="col-sm-8 col-md-9 site-main" style="margin-top: 0">
      <?php if (!empty($h1)): ?>
      <h1 style="margin-top: 6px; margin-bottom: 33px"><?= $h1 ?></h1>
      <?php endif ?>
    <?php $order = $dataProvider->getSort()->getCurrentOrderName(); ?>
    <?= ListView::widget([
        'layout' => '<div class="story-list-filter clearfix">
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
                     <div class="story-pagination">{pager}</div>',
        'summary' => '<span>Показано {count} из {totalCount} историй</span>',
        'dataProvider' => $dataProvider,
        'itemOptions' => ['tag' => false],
        'itemView' => '_storyitem',
        'emptyText' => 'Список историй пуст',
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