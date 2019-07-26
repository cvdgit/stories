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
                'id',
                'title',
                'history_updated_at:datetime',
            ],
        ]) ?>
    </div>
</div>
