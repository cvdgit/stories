<?php

use backend\widgets\grid\order\OrderColumn;
use backend\widgets\grid\PjaxDeleteButton;
use backend\widgets\grid\UpdateButton;
use modules\edu\widgets\AdminToolbarWidget;
use yii\data\DataProviderInterface;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @var $this yii\web\View
 * @var $topicModel modules\edu\models\EduTopic
 * @var $lessonsDataProvider DataProviderInterface
 */

$this->title = 'Редактировать тему';

$this->params['breadcrumbs'] = [
    [
        'label' => $topicModel->classProgram->class->name . ' - ' . $topicModel->classProgram->program->name,
        'url' => ['/edu/admin/class-program/update', 'id' => $topicModel->classProgram->id],
    ],
    $topicModel->name,
];
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <h1 class="h2 page-header">
        <?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/admin/class-program/update', 'id' => $topicModel->classProgram->id]) ?>
        <?= Html::encode($this->title) ?>
    </h1>

    <div class="row">
        <div class="col-lg-6">
            <?= $this->render('_form', [
                'model' => $topicModel,
            ]) ?>
        </div>
        <div class="col-lg-6">

            <div class="header-block">
                <h3 class="h4">Уроки</h3>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group">
                        <?= Html::a('Создать урок', ['/edu/admin/lesson/create', 'topic_id' => $topicModel->id], ['class' => 'btn btn-sm btn-primary']) ?>
                    </div>
                </div>
            </div>

            <div>
                <div id="lessons-grid">
                    <?php Pjax::begin(['id' => 'pjax-lessons']) ?>

                    <?= GridView::widget([
                        'dataProvider' => $lessonsDataProvider,
                        'summary' => false,
                        'options' => ['class' => 'table-responsive'],
                        'columns' => [
                            [
                                'attribute' => 'name',
                                'enableSorting' => false,
                            ],
                            [
                                'attribute' => 'storiesCount',
                                'label' => 'Кол-во историй',
                            ],
                            [
                                'class' => OrderColumn::class,
                                'url' => Url::to(['/edu/admin/topic/order', 'topic_id' => $topicModel->id]),
                                'fieldName' => 'lesson_ids',
                                'container' => '#lessons-grid',
                            ],
                            [
                                'class' => ActionColumn::class,
                                'template' => '{update} {delete}',
                                'buttons' => [
                                    'delete' => static function($url, $model) {
                                        return new PjaxDeleteButton('#', [
                                            'class' => 'pjax-delete-link',
                                            'delete-url' => Url::to(['/edu/admin/lesson/delete', 'id' => $model->id]),
                                            'pjax-container' => 'pjax-lessons',
                                        ]);
                                    },
                                    'update' => static function($url, $model) {
                                        return (new UpdateButton(['/edu/admin/lesson/update', 'id' => $model->id]))();
                                    }
                                ],
                            ],
                        ],
                    ]) ?>

                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
