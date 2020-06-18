<?php

use yii\grid\GridView;

/** @var $this yii\web\View */
/** @var $model common\models\User */
/** @var $dataProvider yii\data\ActiveDataProvider  */

?>
<div class="row">
    <div class="col-lg-12">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'title',
                    'label' => 'История',
                ],
                [
                    'attribute' => 'history_percent',
                    'label' => 'Процент просмотра',
                ],
                [
                    'attribute' => 'history_updated_at',
                    'format' => 'datetime',
                    'label' => 'Дата изменения истории',
                ],
            ],
        ]) ?>
    </div>
</div>
