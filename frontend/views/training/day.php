<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\components\learning\widget\HistoryWidget;
/** @var array $models */
/** @var array $columns */
/** @var frontend\components\learning\form\HistoryFilterForm $filterModel */
?>
<div class="filter__wrap">
    <div class="row">
        <div class="col-md-3">
            <div class="filter-arrow__wrap filter-arrow--left">
                <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i>', '#', [
                    'class' => 'filter-arrow__link',
                    'onclick' => new \yii\web\JsExpression('$("#historyfilterform-action").val("prev"); $("#history-filter-form").submit(); return false'),
                ]) ?>
            </div>
        </div>
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(['id' => 'history-filter-form']) ?>
            <?= $form->field($filterModel, 'date')->widget(\dosamigos\datepicker\DatePicker::class, [
                'language' => 'ru',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy',
                ],
                'options' => [
                    'autocomplete' => 'off',
                ],
                'clientEvents' => [
                    'changeDate' => new \yii\web\JsExpression('function() { $("#history-filter-form").submit(); }'),
                ],
            ])->label(false) ?>
            <?= $form->field($filterModel, 'action')->hiddenInput()->label(false) ?>
            <?php ActiveForm::end() ?>
        </div>
        <div class="col-md-3">
            <div class="filter-arrow__wrap filter-arrow--right">
                <?= Html::a('<i class="glyphicon glyphicon-chevron-right"></i>', '#', [
                    'class' => 'filter-arrow__link',
                    'onclick' => new \yii\web\JsExpression('$("#historyfilterform-action").val("next"); $("#history-filter-form").submit(); return false'),
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