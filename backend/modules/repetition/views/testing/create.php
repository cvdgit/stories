<?php

declare(strict_types=1);

use backend\modules\repetition\Repetition\TestingCreate\CreateRepetitionForm;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/**
 * @var View $this
 * @var CreateRepetitionForm $formModel
 * @var array $studentItems
 * @var array $scheduleItems
 */
?>
<?php $form = ActiveForm::begin(['id' => 'create-repetition-form']); ?>
<?= $form->field($formModel, 'test_name')->textInput(['disabled' => true]); ?>
<?= $form->field($formModel, 'student_id')->dropDownList($studentItems, ['prompt' => 'Выберите ученика']); ?>
<?= $form->field($formModel, 'schedule_id')->dropDownList($scheduleItems, ['prompt' => 'Выберите расписание']); ?>
<div class="form-group">
    <button type="submit" class="btn btn-success">Создать</button>
</div>
<?php ActiveForm::end(); ?>
