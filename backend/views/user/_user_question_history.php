<?php

use backend\widgets\grid\StarColumn;
use yii\grid\GridView;
/** @var $this yii\web\View */
/** @var $model common\models\User */
/** @var $dataProvider yii\data\ActiveDataProvider  */
?>
<div class="row">
    <div class="col-lg-12">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'table-responsive'],
            'columns' => [
                'question_topic_name',
                'created_at:datetime',
                'entity_name',
                'relation_name',
                'correct_answer',
                [
                    'attribute' => 'answers',
                    'value' => function($model) {
                        return implode(', ', array_map(function($item){
                            return $item->answer_entity_name;
                        }, $model->userQuestionAnswers));
                    }
                ],
/*                [
                    'attribute' => 'correct_answers',
                    'label' => 'Прогресс',
                    'class' => StarColumn::class,
                ],*/
            ],
        ]) ?>
    </div>
</div>
