<?php
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\helpers\Html;
/** @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Разделы сайта';
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Создать раздел', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => SerialColumn::class],
            'alias',
            'category.name',
            'title',
            'h1',
            'visible',
            [
                'class' => ActionColumn::class,
                'buttons' => [
                    'view' => function($url, $model) {
                        return (new \backend\widgets\grid\ViewButton(Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/story/index', 'section' => $model->alias])))(['target' => '_blank']);
                    }
                ],
            ],
        ],
    ]) ?>
</div>
