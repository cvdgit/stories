<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use backend\models\question\QuestionType;
/* @var $model backend\models\question\sequence\CreateSequenceQuestion */
?>
<div class="story-test-form">
    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'story_test_id')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'type')->dropDownList(QuestionType::asArray(), ['disabled' => true]) ?>
            <div class="form-group form-group-controls">
                <?= Html::submitButton('Создать вопрос', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-md-6"></div>
    </div>
</div>
