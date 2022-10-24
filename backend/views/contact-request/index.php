<?php

declare(strict_types=1);

use backend\widgets\grid\PjaxDeleteButton;
use yii\bootstrap\Html;
use yii\data\DataProviderInterface;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 */

$this->title = 'Заявки с формы';
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(['id' => 'pjax-contact-requests']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            'name:ntext',
            'phone:ntext',
            'email:email',
            'text:ntext',
            [
                'attribute' => 'comment',
                'format' => 'raw',
                'value' => static function($model) {
                    $text = empty($model->comment) ? 'Добавить комментарий' : $model->comment;
                    return Html::a($text, ['/contact-request/comment', 'id' => $model->id], [
                        'data-pjax' => 0,
                        'data-toggle' => 'modal',
                        'data-target' => '#comment-modal',
                    ]);
                },
            ],
            'created_at:datetime',
            [
                'class' => ActionColumn::class,
                'template' => '{delete}',
                'buttons' => [
                    'delete' => static function($url, $model) {
                        return new PjaxDeleteButton('#', [
                            'class' => 'pjax-delete-link',
                            'delete-url' => Url::to(['/contact-request/delete', 'id' => $model->id]),
                            'pjax-container' => 'pjax-contact-requests',
                        ]);
                    }
                ],
            ],
        ],
    ]) ?>
    <?php Pjax::end(); ?>
</div>

<div class="modal remote fade" tabindex="-1" id="comment-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
(function() {
    $('#comment-modal')
        .on('hide.bs.modal', function() {
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html('');
        });
})();
JS
);
