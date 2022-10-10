<?php

use backend\widgets\grid\order\OrderColumn;
use backend\widgets\grid\PjaxDeleteButton;
use backend\widgets\grid\UpdateButton;
use backend\widgets\grid\ViewButton;
use modules\edu\models\EduLesson;
use yii\data\DataProviderInterface;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var DataProviderInterface $storiesDataProvider
 * @var EduLesson $lessonModel
 */
?>
<div>
    <div class="header-block">
        <h3 class="h4">Истории</h3>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group">
                <?= Html::a('Добавить историю', ['select-story', 'lesson_id' => $lessonModel->id], ['data-toggle' => 'modal', 'data-target' => '#select-story-modal', 'class' => 'btn btn-primary btn-sm']) ?>
                <button id="save-grid-order" type="button" class="btn btn-primary btn-sm">Сохранить порядок</button>
            </div>
        </div>
    </div>

    <div id="stories-grid">
        <?php Pjax::begin(['id' => 'pjax-stories']) ?>
        <?= GridView::widget([
            'dataProvider' => $storiesDataProvider,
            'summary' => false,
            'options' => ['class' => 'table-responsive'],
            'columns' => [
                [
                    'class' => SerialColumn::class,
                ],
                'title',
                [
                    'class' => OrderColumn::class,
                    'url' => Url::to(['/edu/admin/lesson/order', 'lesson_id' => $lessonModel->id]),
                    'fieldName' => 'story_ids',
                    'container' => '#stories-grid',
                ],
                [
                    'class' => ActionColumn::class,
                    'buttons' => [
                        'delete' => static function($url, $model) use ($lessonModel) {
                            return new PjaxDeleteButton('#', [
                                'class' => 'pjax-delete-link',
                                'delete-url' => Url::to(['/edu/admin/lesson/delete-story', 'lesson_id' => $lessonModel->id, 'story_id' => $model->id]),
                                'pjax-container' => 'pjax-stories',
                            ]);
                        },
                        'view' => static function($url, $model) {
                            return (new ViewButton(Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/story/view', 'alias' => $model->alias])))(['target' => '_blank']);
                        },
                        'update' => static function($url, $model) {
                            return (new UpdateButton(['/story/update', 'id' => $model->id]))(['target' => '_blank']);
                        }
                    ],
                ],
            ],
        ]) ?>
        <?php Pjax::end() ?>
    </div>
</div>

<div class="modal remote fade" tabindex="-1" id="select-story-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
(function() {
    $('#select-story-modal')
        .on('hide.bs.modal', function() {
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html('');
        });
})();
JS
);
