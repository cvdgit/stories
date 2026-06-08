<?php

declare(strict_types=1);

use frontend\components\learning\form\WeekFilterForm;
use frontend\components\learning\widget\HistoryWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var array $columns
 * @var array $models
 * @var WeekFilterForm $filterModel
 * @var int $studentId
 * @var string $prevUrl
 * @var string $nextUrl
 * @var bool $canAdmin
 */

$this->registerJs($this->renderFile('@frontend/views/training/week.js'));
?>
<div class="filter__wrap">
    <div class="row">
        <div class="col-md-3">
            <div class="filter-arrow__wrap filter-arrow--left">
                <?= Html::a(
                    '<i class="glyphicon glyphicon-chevron-left"></i>',
                    $prevUrl,
                    ['class' => 'filter-arrow__link']
                ) ?>
            </div>
        </div>
        <div class="col-md-6" style="height: 100%">
            <div style="display: flex; align-items: center; justify-content: center">
                <p style="margin: 0; line-height: 33px"><?= $filterModel->getWeekDatesText() ?></p>
            </div>
            <?php
            $form = ActiveForm::begin([
                'action' => ['week', 'student_id' => $studentId],
                'id' => 'week-filter-form',
                'method' => 'GET'
            ]) ?>
            <?= $form->field($filterModel, 'year')->hiddenInput(['name' => 'year'])->label(false) ?>
            <?= $form->field($filterModel, 'week')->hiddenInput(['name' => 'week'])->label(false) ?>
            <?php if ($canAdmin): ?>
                <div>
                    <?= $form->field($filterModel, 'stat')
                        ->dropDownList($filterModel->getStatItems(), ['name' => 'stat', 'style' => 'max-width: 250px']) ?>
                </div>
            <?php endif ?>
            <?php
            ActiveForm::end() ?>
        </div>
        <div class="col-md-3">
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
        'caption' => 'Количество ответов в тестах (по неделям)',
        'columns' => $columns,
        'models' => $models,
        'tableRowRenderCallback' => static function ($value, array $column) use (
            $studentId
        ): string {
            if (is_array($value)) {
                return Html::a(
                    $value['count'],
                    [
                        '/training/detail-week',
                        'story_id' => $value['storyId'],
                        'student_id' => $studentId,
                        'date' => $column['date'],
                    ],
                    [
                        'class' => 'detail-modal',
                        'style' => 'font-weight: 600; ' . ($value['testRestarts'] ? 'color: rgba(220,53,69,1)' : ''),
                        'title' => $value['testRestarts'],
                        'data-toggle' => 'tooltip',
                    ],
                );
            }
            return (string) $value;
        },
    ]) ?>
</div>
