<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\edu\models\EduClassProgram */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="edu-class-program-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'class_id')->dropDownList($model->getClassArray(), ['prompt' => 'Выберите класс']) ?>
    <?= $form->field($model, 'program_id')->dropDownList($model->getProgramArray(), ['prompt' => 'Выберите предмет']) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
