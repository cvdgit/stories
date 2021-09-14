<?php
use frontend\models\study_task\TaskBeginForm;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/** @var $taskID int */
/** @var $formID string */
$form = ActiveForm::begin(['action' => ['/study/begin'], 'id' => $formID]);
$model = new TaskBeginForm();
$model->task_id = $taskID;
echo $form->field($model, 'task_id')->hiddenInput()->label(false);
echo Html::submitButton('Начать', ['class' => 'btn']);
ActiveForm::end();