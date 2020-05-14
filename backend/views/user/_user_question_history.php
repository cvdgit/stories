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
                'question_topic_name',
                'entity_name',
                'relation_name',
                'correct_answer',
                'created_at:datetime',
            ],
        ]) ?>
    </div>
</div>
