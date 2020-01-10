<?php

use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Изображения';
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => SerialColumn::class],
            'source_url',
            'created_at:datetime',
            [
                'attribute' => 'status',
                'value' => function(\common\models\StorySlideImage $model) {
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
                'controller' => 'editor/image',
                'buttons' => [
                    'view' => function($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            Yii::$app->urlManagerFrontend->createAbsoluteUrl(['image/view', 'id' => $model->hash]),
                            ['target' => '_blank']);
                    }
                ],
            ],
        ],
    ]) ?>
</div>
