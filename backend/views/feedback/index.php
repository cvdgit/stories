<?php

use backend\components\FeedbackPathBuilder;
use common\models\story_feedback\StoryFeedbackStatus;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var integer $status
 * @var FeedbackPathBuilder $builder
 */

$this->title = 'Обратная связь';
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
$this->registerJs($js, View::POS_END);
?>
<div class="category-index">
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <div style="padding-bottom: 20px">
        <?= Nav::widget([
            'options' => ['class' => 'nav nav-tabs material-tabs'],
            'items' => [
                [
                    'label' => 'Новые',
                    'url' => ['index', 'status' => StoryFeedbackStatus::STATUS_NEW],
                    'active' => StoryFeedbackStatus::statusIsNew($status),
                ],
                [
                    'label' => 'Исправлены',
                    'url' => ['index', 'status' => StoryFeedbackStatus::STATUS_DONE],
                    'active' => StoryFeedbackStatus::statusIsDone($status),
                ],
            ],
        ]) ?>
    </div>
    <!--p>
        <?php // Html::a('Исправлены', '#', ['class' => 'btn btn-success', 'onclick' => 'StoryFeedback.markAsDone()']) ?>
    </p-->
    <div class="feedback-grid-wrap">
        <?php Pjax::begin(['id' => 'pjax-feedback']) ?>
        <?php
        $columns = [];

        if (StoryFeedbackStatus::statusIsNew($status)) {
            $columns[] = [
                'format' => 'raw',
                'value' => static function($model) {
                    return Html::button('Исправлено', ['class' => 'btn btn-sm btn-success feedback-success', 'data-feedback-id' => $model->id]);
                },
            ];
        }

        $columns[] = [
            'attribute' => 'text',
            'format' => 'ntext',
            'enableSorting' => false,
        ];
        $columns[] = [
            'label' => 'Путь',
            'format' => 'raw',
            'value' => static function($model) use ($builder) {
                $pathItems = $builder->build($model->id);
                $path = '';
                foreach ($pathItems as $item) {
                    $end = next($pathItems) === false;
                    $path .= Html::a(!$end ? $item['title'] : '<b>' . $item['title'] . '</b>', $item['url'], ['target' => '_blank', 'data-pjax' => 0]) . (!$end ? ' / ' : '');
                }
                return $path;
            },
        ];
        $columns[] = [
            'attribute' => 'created_at',
            'value' => 'created_at',
            'format' => 'datetime',
        ];
        ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'options' => ['class' => 'table-responsive feedback-grid'],
            'columns' => $columns,
        ]) ?>
        <?php Pjax::end() ?>
    </div>
</div>
<?php
$this->registerJs(<<<JS
(function() {
    $('.feedback-grid-wrap').on('click', '.feedback-success', function() {
        $.getJSON('/admin/index.php', {
            r: 'feedback/success',
            id: $(this).attr('data-feedback-id')
        })
            .done(function(response) {
                if (response && response.success) {
                    toastr.success('Успешно');
                    $.pjax.reload('#pjax-feedback', {timeout: 3000});
                }
                else {
                    toastr.error((response && response['message']) || 'Неизвестная ошибка');
                }
            })
            .fail(function(response) {
                toastr.error((response['responseJSON'] && response.responseJSON.message) || 'Произошла ошибка');
            });
    });
})();
JS
);
