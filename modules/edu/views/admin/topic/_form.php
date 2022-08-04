<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\edu\models\EduTopic */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="edu-topic-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'class_program_id')->dropDownList($model->getClassProgramArray(), ['prompt' => 'Выберите программу обучения']) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
