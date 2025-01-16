<?php

declare(strict_types=1);

use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 */

$this->title = 'Промты';
?>
<div>
    <?= GridView::widget([
        'options' => ['class' => 'table-responsive'],
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            'prompt:ntext',
            'created_at:datetime',
            'key',
        ],
    ]) ?>
</div>
