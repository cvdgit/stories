<?php

use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var $searchModel backend\models\SlideVideoSearch */
/** @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Видео';
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Добавить видео', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],
            'title',
            'video_id',
            'created_at:datetime',
            [
                'attribute' => 'status',
                'value' => function(\common\models\SlideVideo $model) {
                    $className = 'ok';
                    $color = '#5cb85c';
                    if (!$model->isSuccess()) {
                        $className = 'remove';
                        $color = '#a94442';
                    }
                    return Html::tag('i', '', ['class' => "glyphicon glyphicon-$className", 'style' => "color: $color"]);
                },
                'format' => 'raw',
            ],
            [
                'class' => ActionColumn::class,
                'buttons' => [
                    'view' => function($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            'https://www.youtube.com/watch?v=' . $model->video_id,
                            ['target' => '_blank']);
                    }
                ],
            ],
        ],
    ]) ?>
</div>
