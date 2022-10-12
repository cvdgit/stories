<?php

declare(strict_types=1);

use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 */
?>
<div class="row">
    <div class="col-lg-12">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'table-responsive'],
            'columns' => [
                [
                    'format' => 'raw',
                    'attribute' => 'name',
                    'value' => static function($model) {
                        return Html::a($model->name, ['history/view', 'id' => $model->id]);
                    }
                ],
                'created_at:datetime',
            ],
        ]) ?>
    </div>
</div>
