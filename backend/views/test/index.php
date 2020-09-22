<?php
use common\models\StoryTest;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Тесты';
$this->params['sidebarMenuItems'] = [
    ['label' => 'Результаты тестов', 'url' => ['test/results']],
];
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<p>
    <?= Html::a('Создать тест', ['create'], ['class' => 'btn btn-primary']) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'options' => ['class' => 'table-responsive'],
    'columns' => [
        'title',
        'header',
        [
            'attribute' => 'source',
            'value' => function($model) {
                return StoryTest::sourceText($model->source);
            }
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
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
        ],
    ],
]) ?>
