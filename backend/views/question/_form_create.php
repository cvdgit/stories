<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use backend\models\question\QuestionType;
/* @var backend\models\question\CreateRegionQuestion $model */
?>
<div class="story-test-form">
    <div class="row">
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'type')->dropDownList(QuestionType::asArray(), ['disabled' => true]) ?>
            <?= $form->field($model, 'imageFile')->fileInput() ?>
            <?= $form->field($model, 'test_id')->hiddenInput()->label(false) ?>
            <div class="form-group form-group-controls">
                <?= Html::submitButton('Создать вопрос', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-lg-6"></div>
    </div>
</div>
