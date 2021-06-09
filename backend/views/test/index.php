<?php
use common\helpers\Url;
use common\models\StoryTest;
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
}

$columns = [];
$columns[] = [
    'attribute' =>'title',
    'format' => 'raw',
    'value' => static function(StoryTest $model) {
        return Html::a($model->title, ['test/update', 'id' => $model->id], ['title' => 'Перейти к редактированию']);
    },
];
if ($searchModel->isWordList()) {
    $columns[] = [
        'attribute' => 'wordList.name',
        'label' => 'Список слов',
    ];
}
if ($searchModel->isNeoTest()) {
    $columns[] = [
        'attribute' => 'childrenTestsCount',
        'label' => 'Количество вариантов',
    ];
}
if (!$searchModel->isNeoTest() && !$searchModel->isWordList()) {
    $columns[] = [
        'attribute' => 'questionsNumber',
        'label' => 'Вопросов',
    ];
}
if (!$searchModel->isNeoTest()) {
    $columns[] = [
        'attribute' => 'answer_type',
        'value' => static function($model) {
            return AnswerType::asText($model->answer_type);
        },
        'filter' => AnswerType::asArray(),
    ];

}
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
if (!$searchModel->isNeoTest()) {
    $columns[] = [
        'attribute' => 'transition',
        'label' => 'Переход',
        'format' => 'raw',
        'value' => function ($model) {
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
}

$columns[] = [
    'class' => 'yii\grid\ActionColumn',
    'template' => '{update} {delete}',
    'urlCreator' => static function($action, $model, $key, $index) {
        $url = '';
        if ($action === 'update') {
            $urlParam = ['test/update', 'id' => $model->id];
            if ($model->isVariant()) {
                $urlParam['id'] = $model->parent_id;
                $urlParam['#'] = $model->id;
            }
            $url = Url::to($urlParam);
        }
        if ($action === 'delete') {
            $id = $model->id;
            if ($model->isVariant()) {
                $id = $model->parent_id;
            }
            $url = Url::to(['test/delete', 'id' => $id]);
        }
        return $url;
    },
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
