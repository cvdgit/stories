<?php

use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var $model common\models\SlideVideo */
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
        'columns' => [
            ['class' => SerialColumn::class],
            'title',
            'video_id',
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
