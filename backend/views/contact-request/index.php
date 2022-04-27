<?php
use yii\bootstrap\Html;
use yii\grid\GridView;
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = 'Заявки с формы';
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            'name:ntext',
            'phone:ntext',
            'email:email',
            'text:ntext',
            'created_at:datetime',
            [
                'class' => yii\grid\ActionColumn::class,
                'template' => '{delete}',
            ],
        ],
    ]) ?>
</div>
