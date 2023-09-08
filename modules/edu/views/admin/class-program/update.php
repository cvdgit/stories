<?php

declare(strict_types=1);

use backend\widgets\grid\order\OrderColumn;
use backend\widgets\grid\PjaxDeleteButton;
use modules\edu\models\EduClass;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduProgram;
use modules\edu\Teacher\ClassProgram\Update\UpdateClassProgramForm;
use modules\edu\widgets\AdminToolbarWidget;
use yii\bootstrap\ActiveForm;
use yii\data\DataProviderInterface;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var EduClassProgram $model
 * @var DataProviderInterface $topicsDataProvider
 * @var UpdateClassProgramForm $formModel
 */

$this->title = 'Программа обучения';
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <h1 class="h2 page-header">
        <?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/admin/class-program/index']) ?>
        <?= Html::encode($this->title) ?>
    </h1>

    <div class="row">
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($formModel, 'class_id')->dropDownList(ArrayHelper::map(EduClass::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'), ['prompt' => 'Выберите класс']) ?>
            <?= $form->field($formModel, 'program_id')->dropDownList(ArrayHelper::map(EduProgram::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'), ['prompt' => 'Выберите предмет']) ?>
            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="header-block">
                <h3 class="h4">Темы</h3>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group">
                        <?= Html::a('Добавить тему', ['/edu/admin/topic/create', 'class_program_id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
                        <button id="save-grid-order" type="button" class="btn btn-primary btn-sm">Сохранить порядок</button>
                    </div>
                </div>
            </div>
            <div>
                <div id="topics-grid">
                    <?php Pjax::begin(['id' => 'pjax-topics']) ?>
                    <?= GridView::widget([
                        'dataProvider' => $topicsDataProvider,
                        'summary' => false,
                        'columns' => [
                            [
                                'attribute' => 'order',
                                'label' => '',
                            ],
                            [
                                'attribute' => 'name',
                                'enableSorting' => false,
                                'format' => 'raw',
                                'value' => static function($model) {
                                    return Html::a($model->name, ['/edu/admin/topic/update', 'id' => $model->id]);
                                }
                            ],
                            [
                                'attribute' => 'lessonsCount',
                                'label' => 'Кол-во уроков',
                            ],
                            [
                                'class' => OrderColumn::class,
                                'url' => Url::to(['/edu/admin/class-program/order', 'class_program_id' => $model->id]),
                                'fieldName' => 'topic_ids',
                                'container' => '#topics-grid',
                            ],
                            [
                                'class' => ActionColumn::class,
                                'template' => '{update} {delete}',
                                'controller' => 'admin/topic',
                                'buttons' => [
                                    'delete' => static function($url, $model) {
                                        return new PjaxDeleteButton('#', [
                                            'class' => 'pjax-delete-link',
                                            'delete-url' => Url::to(['/edu/admin/topic/delete', 'id' => $model->id]),
                                            'pjax-container' => 'pjax-topics',
                                        ]);
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
