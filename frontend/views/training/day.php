<?php

declare(strict_types=1);

use dosamigos\datepicker\DatePicker;
use frontend\components\learning\form\HistoryFilterForm;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;
use frontend\components\learning\widget\HistoryWidget;

/**
 * @var View $this
 * @var array $models
 * @var array $columns
 * @var HistoryFilterForm $filterModel
 * @var int $studentId
 * @var string $prevUrl
 * @var string $nextUrl
 */

$this->registerJs($this->renderFile('@frontend/views/training/day.js'));
?>
<div class="filter__wrap">
    <div class="row">
        <div class="col-md-2">
            <div class="filter-arrow__wrap filter-arrow--left">
                <?= Html::a(
                    '<i class="glyphicon glyphicon-chevron-left"></i>',
                    $prevUrl,
                    ['class' => 'filter-arrow__link']
                ) ?>
            </div>
        </div>
        <div class="col-md-8">
            <?php
            $form = ActiveForm::begin(
                [
                    'action' => ['index', 'student_id' => $studentId],
                    'id' => 'history-filter-form',
                    'method' => 'get',
                    'options' => ['style' => 'display: flex; margin-right: 3rem'],
                ],
            ) ?>
            <div style="flex: 1; margin-right: 3rem">
                <?= $form->field($filterModel, 'date')->widget(DatePicker::class, [
                    'language' => 'ru',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'name' => 'date',
                    ],
                    'clientEvents' => [
                        'changeDate' => new JsExpression('function() { $("#history-filter-form").submit(); }'),
                    ],
                ])->label(false) ?>
            </div>
            <div>
                <?= $form->field($filterModel, 'hours')->dropDownList($filterModel->getHoursDropdown(), ['name' => 'hours'])->label(false) ?>
            </div>
            <?php
            ActiveForm::end() ?>
        </div>
        <div class="col-md-2">
            <div class="filter-arrow__wrap filter-arrow--right">
                <?= Html::a(
                    '<i class="glyphicon glyphicon-chevron-right"></i>',
                    $nextUrl,
                    ['class' => 'filter-arrow__link']
                ) ?>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive">
    <?= HistoryWidget::widget([
        'caption' => 'Количество ответов в тестах (за день)',
        'columns' => $columns,
        'models' => $models,
        'tableRowRenderCallback' => static function ($value, array $column) use (
            $studentId,
            $filterModel
        ): string {
            if (is_array($value)) {
                $value = Html::a(
                    $value['count'],
                    [
                        '/training/detail',
                        'story_id' => $value['storyId'],
                        'student_id' => $studentId,
                        'date' => $filterModel->date,
                        'hours' => $filterModel->hours,
                        'time' => $column['label'],
                    ],
                    [
                        'class' => 'detail-modal',
                        'style' => 'font-weight: 600',
                        'title' => $value['testRestarts'],
                        'data-toggle' => 'tooltip',
                    ],
                );
            }
            return (string) $value;
        },
    ]) ?>
</div>
