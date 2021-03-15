<?php
use common\models\test\AnswerType;
use common\models\test\SourceType;
use dosamigos\datepicker\DatePicker;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $source int */
/* @var $sourceRecordsTotal int */
$this->title = 'Тесты';
$this->params['sidebarMenuItems'] = [
    ['label' => 'Результаты тестов', 'url' => ['test/results']],
];
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<p>
    <?= Html::a('Создать тест', ['create', 'source' => $source], ['class' => 'btn btn-primary']) ?>
</p>
<?= Nav::widget([
    'options' => ['class' => 'nav nav-tabs material-tabs'],
    'items' => SourceType::asNavItems($source),
]) ?>
<?php

if (Yii::$app->user->can('admin') && $searchModel->isNeoTest()) {
    echo Html::tag(
        'div',
        Html::a('Очистить историю по всем тестам (' . ($sourceRecordsTotal === 0 ? 'нет записей' : $sourceRecordsTotal) . ')', ['history/clear-all-by-source', 'source' => $source], ['class' => 'btn btn-danger pull-right']),
        ['class' => 'clearfix', 'style' => 'padding: 20px 0 0 0']);
    $js = <<< JS

JS;
    $this->registerJs($js);
}

$columns = [];
$columns[] = 'title';
if ($searchModel->isWordList()) {
    $columns[] = [
        'attribute' => 'wordList.name',
        'label' => 'Список слов',
    ];
}
if ($searchModel->isNeoTest()) {
    $columns[] = [
        'attribute' => 'parentTest.title',
        'label' => 'Основной тест',
    ];
}
$columns[] = [
    'attribute' => 'answer_type',
    'value' => function($model) {
        return AnswerType::asText($model->answer_type);
    },
    'filter' => AnswerType::asArray(),
];
$columns[] = [
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
];
$columns[] = [
    'attribute' => 'transition',
    'label' => 'Переход',
    'format' => 'raw',
    'value' => function($model) {
        $html = '';
        $stories = $model->stories;
        if (count($stories) > 0) {
            $story = $stories[0];
            $html = Html::a('к истории', Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $story->alias]), ['target' => '_blank']);
        }
        if ($model->haveWordList()) {
            if (!empty($html)) {
                $html .= '<br/>';
            }
            $html .= Html::a('к списку слов', \common\models\TestWordList::getUpdateUrl($model->word_list_id), ['target' => '_blank']);
        }
        return $html;
    }
];

$columns[] = [
    'class' => 'yii\grid\ActionColumn',
    'template' => '{update} {delete}',
    'buttons' => [
        'update' => function($url, $model) {
            $urlParam = ['test/update', 'id' => $model->id];
            if ($model->isVariant()) {
                $urlParam['id'] = $model->parent_id;
                $urlParam['#'] = $model->id;
            }
            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $urlParam);
        },
        'delete' => function($url, $model) {
            $id = $model->id;
            if ($model->isVariant()) {
                $id = $model->parent_id;
            }
            return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['test/delete', 'id' => $id]);
        }
    ],
];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'options' => ['class' => 'table-responsive test-grid'],
    'columns' => $columns,
]) ?>

<?php
$css = <<<CSS
.test-grid {
    margin-top: 20px;
}
.test-grid .summary {
    text-align: right;
}
CSS;
$this->registerCss($css);
