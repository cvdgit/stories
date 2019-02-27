<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Опечатки';

$action = Url::to(['/feedback/batchupdate']);
$js = <<< JS
var StoryFeedback = (function() {

    function send(data) {
        return $.ajax({
            url: '$action',
            type: 'POST',
            dataType: 'json',
            data: {
                'data': data
            }
        });
    }

    function markAsDone() {
        var keys = $('#w0').yiiGridView('getSelectedRows');
        send(keys)
            .done(function(data) {
                if (data.success) {
                    //$("#w0").yiiGridView("applyFilter");
                    $.pjax.reload({container: "#pjax-feedback"});
                    StoryAlert.success('Успешно');
                }
            })
            .fail(function(data) {
                StoryAlert.error(data.responseText);
            });
    }

    return {
        markAsDone: markAsDone
    };
})();
JS;
$this->registerJs($js, yii\web\View::POS_END);
?>
<div class="category-index">
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <div id="alert_placeholder"></div>
    <p>
        <?= Html::a('Исправлены', '#', ['class' => 'btn btn-success', 'onclick' => 'StoryFeedback.markAsDone()']) ?>
    </p>
    <?php yii\widgets\Pjax::begin(['id' => 'pjax-feedback']) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
            ],
            'story.title',
            'slide_number',
            'assignUser.username',
            'text',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->getStatusText();
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => 'created_at',
                'format' => 'datetime',
            ]
        ],
    ]) ?>
    <?php yii\widgets\Pjax::end() ?>
</div>
