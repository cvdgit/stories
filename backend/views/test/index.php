<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Тесты';
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<p>
    <?= Html::a('Новый тест', ['create'], ['class' => 'btn btn-primary']) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'table-responsive'],
    'columns' => [
        'id',
        'title',
        'description',
        'status',
        'created_at:datetime',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
        ],
    ],
]) ?>
