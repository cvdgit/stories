<?php

use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var $model common\models\SlideVideo */
/** @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Плейлисты';
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => SerialColumn::class],
            'title',
            [
                'attribute' => 'stories',
                'label' => 'Количество историй',
                'value' => function($model) {
                    return count($model->stories);
                }
            ],
            'created_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]) ?>
</div>
