<?php
use backend\widgets\WikidsDatePicker;
use common\helpers\Url;
use common\models\Category;
use common\models\story\StoryStatus;
use yii\bootstrap\Nav;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use common\helpers\UserHelper;
use common\models\Story;
use yii\widgets\Menu;
use yii\widgets\Pjax;
/** @var $this yii\web\View */
/** @var $dataProvider yii\data\ActiveDataProvider */
/** @var $searchModel backend\models\StorySearch */
/** @var $batchForm backend\models\StoryBatchCommandForm */
/** @var $status StoryStatus */
$this->title = 'Управление историями';
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<div style="display: flex; flex-direction: row; column-gap: 10px; margin: 10px 0">
    <?= Html::a('Создать историю', ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Создать историю (AI)', ['/story-ai/create'], ['class' => 'btn btn-primary']) ?>
</div>

<?= Nav::widget([
    'options' => ['class' => 'nav nav-tabs material-tabs'],
    'items' => [
        [
            'label' => 'Черновики',
            'url' => ['story/index', 'status' => StoryStatus::DRAFT],
            'active' => $status->isDraft(),
        ],
        [
            'label' => 'Опубликованные',
            'url' => ['story/index', 'status' => StoryStatus::PUBLISHED],
            'active' => $status->isPublished(),
        ],
        [
            'label' => 'На публикацию',
            'url' => ['story/index', 'status' => StoryStatus::FOR_PUBLICATION],
            'active' => $status->isForPublication(),
        ],
        [
            'label' => 'Задания',
            'url' => ['story/index', 'status' => StoryStatus::TASK],
            'active' => $status->statusIisTask(),
            'visible' => Yii::$app->user->can(common\rbac\UserRoles::ROLE_TEACHER),
        ],
    ],
]) ?>
<?php
$columns = [
    'id',
    [
        'attribute' =>'title',
        'format' => 'raw',
        'value' => static function(Story $model) {
            return Html::a($model->title, ['story/update', 'id' => $model->id], ['title' => 'Перейти к редактированию']);
        },
    ],
    [
        'format' => 'raw',
        'attribute' => 'mode',
        'value' => static function($model) {
            $mode = '';
            if ($model->isAudioStory()) {
                $mode = '<i class="glyphicon glyphicon-volume-up" data-toggle="popover" title="Озвучено" style="font-size: 20px; color: #d9534f"></i>';
            }
            if ($model->hasNeoRelation()) {
                $mode .= '<i class="glyphicon glyphicon glyphicon-globe" data-toggle="popover" title="Есть связь с Neo4j" style="font-size: 20px; color: #d9534f"></i>';
            }
            return $mode;
        }
    ],
    [
        'attribute' => 'user_id',
        'value' => 'author.username',
        'filter' => UserHelper::getStoryAuthorArray(),
    ],
    [
        'attribute' => 'story_categories',
        'value' => static function($model) {
            return implode(', ', array_map(static function($item){
                return $item->name;
            }, $model->categories));
        },
        'filter' => Html::a('Категории', '#select-categories-modal', ['data-toggle' => 'modal'])
            . Html::activeHiddenInput($searchModel, 'category_id')
    ],
];
if ($status->isDraft()) {
    $columns[] = [
        'attribute' => 'created_at',
        'value' => 'created_at',
        'format' => 'datetime',
        'filter' => WikidsDatePicker::widget([
            'model' => $searchModel,
            'attribute' => 'created_at',
        ]),
    ];
    $columns[] = [
        'attribute' => 'updated_at',
        'value' => 'updated_at',
        'format' => 'datetime',
        'filter' => WikidsDatePicker::widget([
            'model' => $searchModel,
            'attribute' => 'updated_at',
        ]),
    ];
}
if ($status->isPublished()) {
    $columns[] = [
        'attribute' => 'published_at',
        'value' => 'published_at',
        'format' => 'datetime',
        'filter' => WikidsDatePicker::widget([
            'model' => $searchModel,
            'attribute' => 'published_at',
        ]),
    ];
    $columns[] = 'views_number';
}
$columns[] = [
    'class' => ActionColumn::class,
    'buttons' => [
        'view' => static function($url, $model) {
            return (new \backend\widgets\grid\ViewButton(Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $model->alias])))(['target' => '_blank']);
        }
    ],
];

?>
<?php Pjax::begin(['id' => 'pjax-stories']) ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'options' => ['class' => 'table-responsive'],
    'columns' => $columns,
]) ?>
<?php Pjax::end(); ?>

<?= $this->render('_categories', ['treeID' => null, 'selectInputID' => 'storysearch-category_id']) ?>

<?php
$css = <<< CSS
#category-list ul {
    list-style: none;
}
CSS;
$this->registerCss($css);
$js = <<< JS
$('#save-categories').off('click').on('click', function() {
    var list = $('#selected-category-list'),
        id = 'storysearch-category_id',
        ids = [];
    list.empty();
    $('#category-list input[type=checkbox]').each(function() {
        var el = $(this);
        if (el.is(':checked')) {
            $('<span>')
              .addClass('label label-default')
              .text($.trim(el.parent().text()))
              .appendTo(list);
            list.append(' ');
            ids.push(el.val());
        }
    });
    $('#' + id).val(ids.join(',')).trigger('change');
    $('#select-categories-modal').modal('hide');
});
JS;
$this->registerJs($js);
