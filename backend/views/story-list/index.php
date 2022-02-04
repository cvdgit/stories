<?php
use yii\grid\GridView;
use yii\helpers\Html;
$this->title = 'Списки историй';
/** @var $dataProvider yii\data\ActiveDataProvider */
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Создать список', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => yii\grid\SerialColumn::class],
            'id',
            'name',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'class' => yii\grid\ActionColumn::class,
            ],
        ],
    ]) ?>
</div>