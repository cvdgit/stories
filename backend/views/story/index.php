<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;
use common\models\Category;
use common\models\Story;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Истории';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<p>
    <?= Html::a('Создать историю', ['create'], ['class' => 'btn btn-success']) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'title',
        [
            'attribute' => 'user_id',
            'value' => 'author.username',
            'filter' => User::getUserArray(),
        ],
        [
            'attribute' => 'category_id',
            'value' => 'category.name',
            'filter' => Category::getCategoryArray(),
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
                    'format' => 'dd.m.yyyy'
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
                    'format' => 'dd.m.yyyy'
                ]
            ]),
        ],
        [
            'attribute' => 'status',
            'value' => function($model) {
                return $model->getStatusText();
            },
            'filter' => Story::getStatusArray(),
        ],
        [
            'attribute' => 'sub_access',
            'value' => function($model) {
                return $model->getSubAccessText();
            },
            'filter' => Story::getSubAccessArray(),
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'buttons' => [
                'view' => function($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $model->alias]));
                }
            ],
        ],
    ],
]) ?>