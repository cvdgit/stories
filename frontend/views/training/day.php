<?php

declare(strict_types=1);

use dosamigos\datepicker\DatePicker;
use frontend\components\learning\form\HistoryFilterForm;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;
use frontend\components\learning\widget\HistoryWidget;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var array $models
 * @var array $columns
 * @var HistoryFilterForm $filterModel
 */

$this->registerJs($this->renderFile('@frontend/views/training/day.js'));
?>
<?php Pjax::begin(['id' => 'pjax-day-history']) ?>
<div class="filter__wrap">
    <div class="row">
        <div class="col-md-2">
            <div class="filter-arrow__wrap filter-arrow--left">
                <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i>', '#', [
                    'class' => 'filter-arrow__link',
                    'onclick' => new JsExpression('$("#historyfilterform-action").val("prev"); $("#history-filter-form").submit(); return false'),
                ]) ?>
            </div>
        </div>
        <div class="col-md-8">
            <?php $form = ActiveForm::begin(['id' => 'history-filter-form', 'method' => 'get', 'options' => ['style' => 'display: flex; margin-right: 3rem']]) ?>
            <div style="flex: 1; margin-right: 3rem">
                <?= $form->field($filterModel, 'date')->widget(DatePicker::class, [
                    'language' => 'ru',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                    ],
                    'clientEvents' => [
                        'changeDate' => new JsExpression('function() { $("#history-filter-form").submit(); }'),
                    ],
                ])->label(false) ?>
            </div>
            <div>
                <?= $form->field($filterModel, 'hours')->dropDownList($filterModel->getHoursDropdown())->label(false) ?>
                <?= $form->field($filterModel, 'action')->hiddenInput()->label(false) ?>
            </div>
            <?php ActiveForm::end() ?>
        </div>
        <div class="col-md-2">
            <div class="filter-arrow__wrap filter-arrow--right">
                <?= Html::a('<i class="glyphicon glyphicon-chevron-right"></i>', '#', [
                    'class' => 'filter-arrow__link',
                    'onclick' => new JsExpression('$("#historyfilterform-action").val("next"); $("#history-filter-form").submit(); return false'),
                ]) ?>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive">
<?= HistoryWidget::widget([
    'caption' => 'Количество ответов в тестах (за день)',
    'columns' => $columns,
    'models' => $models,
]) ?>
</div>
<?php Pjax::end() ?>
