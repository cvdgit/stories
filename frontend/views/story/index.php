<?php
use asu\tagcloud\TagCloud;
use common\models\Tag;
use common\rbac\UserRoles;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;
use yii\widgets\Menu;
use common\models\Category;
/** @var $this yii\web\View */
/** @var $searchModel frontend\models\StorySearch */
/** @var $dataProvider yii\data\ActiveDataProvider */
/** @var $action array */
/** @var $emptyText string */
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
?>
<div class="container">
    <div class="row">
        <nav class="col-xs-12 col-sm-12 col-md-12 col-lg-3 site-sidebar">
            <div class="story-filter-btn-wrapper text-center" style="margin-bottom: 20px">
                <button class="btn story-filter-btn" data-toggle="collapse" data-target=".site-sidebar-wrapper">Категории и поиск</button>
            </div>
            <div class="site-sidebar-wrapper">
                <?php $form = ActiveForm::begin([
                  'action' => $action,
                  'method' => 'GET',
                  'options' => ['class' => 'story-form'],
                ]); ?>
                <div style="position: relative">
                    <?= $form->field($searchModel, 'title', ['inputOptions' => ['class' => 'form-control story-search-control']])
                             ->textInput(['placeholder' => 'Поиск...', 'autocomplete' => 'off'])
                             ->label(false) ?>
                    <span class="icon icon-search"></span>
                </div>
                <?php ActiveForm::end(); ?>
                <?php if (!Yii::$app->user->isGuest): ?>
                <h2>Личное</h2>
                <?= Menu::widget([
                    'items' => [
                        ['label' => 'История просмотра', 'url' => ['/story/history']],
                        ['label' => 'Понравившиеся', 'url' => ['/story/liked']],
                        ['label' => 'Избранное', 'url' => ['/story/favorites']],
                        ['label' => 'Моя озвучка', 'url' => ['/story/myaudio']],
                    ],
                    'options' => ['class' => 'story-category-list'],
                ]) ?>
                <?php endif ?>
                <h2>Категории</h2>
                <?= Menu::widget([
                    'items' => Category::getCategoriesForMenu(),
                    'submenuTemplate' => "\n<ul class=\"story-category-list story-sub-category-list\">\n{items}\n</ul>\n",
                    'options' => ['class' => 'story-category-list'],
                ]) ?>
                <h2>Облако тегов</h2>
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
            </div>
        </nav>
        <main class="col-xs-12 col-sm-12 col-md-12 col-lg-9 site-main" style="margin-top: 0">
            <h1 style="margin-top: 0; margin-bottom: 20px"><?= $this->getHeader() ?></h1>
            <div class="story-popular-categories row row-no-gutters">
                <div class="col-md-6">
                    <?= Menu::widget([
                        'items' => [
                            ['label' => '#АудиоСказки', 'url' => ['/story/audio-stories'], 'active' => Yii::$app->controller->id === 'story' && Yii::$app->controller->action->id === 'audio-stories'],
                            ['label' => '#СказкиНаНочь', 'url' => ['/story/bedtime-stories'], 'active' => Yii::$app->controller->id === 'story' && Yii::$app->controller->action->id === 'bedtime-stories'],
                        ],
                        'options' => ['class' => 'list-inline'],
                    ]) ?>
                </div>
                <div class="col-md-6 text-right">
                    <?php if ($category !== null && UserRoles::canModerator()): ?>
                    <?= Html::a('Тесты', ['/test/index', 'category_id' => $category->id]) ?> |
                    <?php endif ?>
                    <?= Html::a('Случайная сказка', ['/story/random'], ['style' => 'color: #d9534f']) ?>
                </div>
            </div>
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
                'itemView' => get_class($dataProvider) === \yii\data\ActiveDataProvider::class ? '_storyitem' : '_storyitem_array',
                'emptyText' => $emptyText,
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
