<?php

use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var $model common\models\Story */
/** @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Озвучка';
$this->params['sidebarMenuItems'] = [
    ['label' => 'Вернуться к истории', 'url' => ['story/update', 'id' => $model->id]],
    ['label' => 'Редактор', 'url' => ['editor/edit', 'id' => $model->id]],
    ['label' => 'Статистика', 'url' => ['statistics/list', 'id' => $model->id]],
    ['label' => 'Озвучка', 'url' => ['audio/index', 'story_id' => $model->id]],
];
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Создать дорожку', ['create', 'story_id' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => SerialColumn::class],
            'name',
            [
                'attribute' => 'type',
                'value' => function($model) {
                    return $model->audioTypeValue();
                },
            ],
            [
                'attribute' => 'default',
                'value' => function($model) {
                    return $model->isDefault() ? 'Да' : 'Нет';
                }
            ],
            'user.username',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    if ($model->isOriginal()) {
                        return $model->getStatusText();
                    }
                    return '';
                },
                'filter' => \common\models\StoryAudioTrack::getStatusArray(),
            ],
            'created_at:datetime',
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
            ],
        ],
    ]) ?>
</div>
