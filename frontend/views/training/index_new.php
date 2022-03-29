<?php
use frontend\components\learning\widget\HistoryWidget;
use yii\bootstrap\Nav;
use yii\widgets\ActiveForm;
use yii\bootstrap\Html;
/** @var array $columns */
/** @var array $models */
/** @var common\models\UserStudent[] $students */
/** @var int $activeStudentId */
/** @var frontend\components\learning\form\HistoryFilterForm $filterModel */
$title = 'Прогресс обучения';
$this->setMetaTags($title,
    $title,
    '',
    $title);
$items = [];
foreach ($students as $student) {
    $items[] = [
        'label' => $student->name,
        'url' => ['training/index', 'student_id' => $student->id],
        'active' => $student->id === $activeStudentId,
    ];
}
$this->registerCss(<<<CSS
.filter__wrap {
    padding: 2rem 0;
}
.filter-arrow--left {
    text-align: left;
}
.filter-arrow--right {
    text-align: right;
}
.filter-arrow__link {
    display: inline-block;
}
.filter-arrow__link i {
    font-size: 3rem;
    line-height: 3rem;
}
CSS
);
?>
<div>
    <h1>Прогресс <span>обучения</span></h1>
    <?= Nav::widget([
        'options' => ['class' => 'nav nav-tabs material-tabs'],
        'items' => $items,
    ]) ?>
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
    <?= HistoryWidget::widget([
        'caption' => 'Количество ответов в тестах',
        'columns' => $columns,
        'models' => $models,
    ]) ?>
</div>