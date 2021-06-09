<?php
use common\models\Category;
use common\models\story\StoryStatus;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use common\helpers\UserHelper;
use common\models\Story;
use dosamigos\datepicker\DatePicker;
use yii\widgets\Menu;
use yii\widgets\Pjax;
/** @var $this yii\web\View */
/** @var $dataProvider yii\data\ActiveDataProvider */
/** @var $searchModel backend\models\StorySearch */
/** @var $batchForm backend\models\StoryBatchCommandForm */
$this->title = 'Управление историями';
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<p>
    <?= Html::a('Создать историю', ['create'], ['class' => 'btn btn-success']) ?>
</p>
<?php Pjax::begin(['id' => 'pjax-stories']) ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'options' => ['class' => 'table-responsive'],
    'columns' => [
        // ['class' => CheckboxColumn::class],
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
            'value' => function($model) {
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
            'filter' => UserHelper::getUserArray(),
        ],
        [
            'attribute' => 'story_categories',
            'value' => function($model) {
                return implode(', ', array_map(function($item){
                    return $item->name;
                }, $model->categories));
            },
            'filter' => Html::a('Категории', '#filter-categories-modal', ['data-toggle' => 'modal'])
                        . Html::activeHiddenInput($searchModel, 'category_id')
        ],
        [
            'attribute' => 'created_at',
            'value' => 'created_at',
            'format' => 'datetime',
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'created_at',
                'language' => 'ru',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy'
                ]
            ]),
        ],
        [
            'attribute' => 'updated_at',
            'value' => 'updated_at',
            'format' => 'datetime',
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'updated_at',
                'language' => 'ru',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy'
                ]
            ]),
        ],
        [
            'attribute' => 'status',
            'value' => static function(Story $model) {
                return StoryStatus::asText($model->status);
            },
            'filter' => StoryStatus::asArray(),
        ],
        [
            'attribute' => 'sub_access',
            'value' => function($model) {
                return $model->getSubAccessText();
            },
            'filter' => Story::getSubAccessArray(),
        ],
        'views_number',
        [
            'class' => ActionColumn::class,
            'buttons' => [
                'view' => static function($url, $model) {
                    return (new \backend\widgets\grid\ViewButton(Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $model->alias])))(['target' => '_blank']);
                }
            ],
        ],
    ],
]) ?>
<?php Pjax::end(); ?>

<div class="modal fade" id="filter-categories-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Категории</h4>
            </div>
            <div class="modal-body">
                <div id="category-list">
                    <?= Menu::widget([
                        'items' => Category::categoryArray(),
                        'encodeLabels' => false,
                        'linkTemplate' => '<label><input type="checkbox" value="{url}"> {label}</label>',
                    ]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="do-category-filter">Применить</button>
                <button class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

<?php
$css = <<< CSS
#category-list ul {
    list-style: none;
}
CSS;
$this->registerCss($css);
$js = <<< JS
$('#filter-categories-modal').on('show.bs.modal', function() {
    var list = $('#category-list'),
        id = 'storysearch-category_id';
    $('input[type=checkbox]', list).prop('checked', false);
    var value = $('#' + id).val();
    if (value) {
        value.split(',').forEach(function(value) {
            $('input[value=' + value + ']', list).prop('checked', true);
        });
    }
});
$('#do-category-filter').on('click', function() {
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
    $('#filter-categories-modal').modal('hide');
});
JS;
$this->registerJs($js);
?>