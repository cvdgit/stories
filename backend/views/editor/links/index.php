<?php

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
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'title',
            'href',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>
</div>
