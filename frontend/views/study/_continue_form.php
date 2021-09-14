<?php
use frontend\models\study_task\TaskContinueForm;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/** @var $taskID int */
/** @var $formID string */
$form = ActiveForm::begin(['action' => ['/study/continue'], 'id' => $formID]);
$model = new TaskContinueForm();
$model->task_id = $taskID;
echo $form->field($model, 'task_id')->hiddenInput()->label(false);
echo Html::submitButton('Продолжить', ['class' => 'btn']);
ActiveForm::end();