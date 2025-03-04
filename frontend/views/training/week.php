<?php

declare(strict_types=1);

use frontend\components\learning\form\WeekFilterForm;
use frontend\components\learning\widget\HistoryWidget;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var array $columns
 * @var array $models
 * @var WeekFilterForm $filterModel
 */

$this->registerJs($this->renderFile('@frontend/views/training/week.js'));
?>
<?php Pjax::begin(['id' => 'pjax-week-history']) ?>
<div class="filter__wrap">
    <div class="row">
        <div class="col-md-3">
            <div class="filter-arrow__wrap filter-arrow--left">
                <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i>', '#', [
                    'class' => 'filter-arrow__link',
                    'onclick' => new JsExpression('$("#weekfilterform-action").val("prev"); $("#week-filter-form").submit(); return false'),
                ]) ?>
            </div>
        </div>
        <div class="col-md-6" style="height: 100%">
            <div style="display: flex; align-items: center; justify-content: center">
                <p style="margin: 0; line-height: 33px"><?= $filterModel->getWeekDatesText() ?></p>
            </div>
            <?php $form = ActiveForm::begin(['id' => 'week-filter-form', 'method' => 'GET']) ?>
            <?= $form->field($filterModel, 'week')->hiddenInput()->label(false) ?>
            <?= $form->field($filterModel, 'action')->hiddenInput()->label(false) ?>
            <?php ActiveForm::end() ?>
        </div>
        <div class="col-md-3">
            <div class="filter-arrow__wrap filter-arrow--right">
                <?= Html::a('<i class="glyphicon glyphicon-chevron-right"></i>', '#', [
                    'class' => 'filter-arrow__link',
                    'onclick' => new JsExpression('$("#weekfilterform-action").val("next"); $("#week-filter-form").submit(); return false'),
                ]) ?>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive">
<?= HistoryWidget::widget([
    'caption' => 'Количество ответов в тестах (по неделям)',
    'columns' => $columns,
    'models' => $models,
]) ?>
</div>
<?php Pjax::end() ?>
