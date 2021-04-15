<?php
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
/** @var $model common\models\StorySlide */
/** @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Ссылки слайда #' . $model->number;
?>
<div>
    <h1 class="page-header">Ссылки: <?= Html::a('слайд #' . $model->number, ['editor/edit', 'id' => $model->story->id, '#' => $model->id]) . ', ' . Html::a($model->story->title, ['editor/edit', 'id' => $model->story->id]) ?></h1>
    <p>
        <?= Html::a('Создать ссылку', ['create', 'slide_id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('YouTube', ['youtube-create', 'slide_id' => $model->id], ['class' => 'btn btn-danger']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'title',
            'href:url',
            [
                'class' => ActionColumn::class,
                'buttons' => [
                    'update' => function($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
                            $model->getUpdateUrl());
                    },
                    'view' => function($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            $model->href,
                            ['target' => '_blank']);
                    },
                ],
            ],
        ],
    ]) ?>
</div>
