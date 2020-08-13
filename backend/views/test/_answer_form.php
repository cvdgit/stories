<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $this yii\web\View */
/** @var $model common\models\StoryTestAnswer */
/** @var $form yii\widgets\ActiveForm */
/** @var $answerImageModel backend\models\AnswerImageUploadForm */
?>
<div class="story-test-form">
    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'story_question_id')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($answerImageModel, 'answerImage')->fileInput() ?>
            <?= $form->field($model, 'is_correct')->checkbox() ?>
            <div class="form-group">
                <?= Html::submitButton(($model->isNewRecord ? 'Создать' : 'Изменить') . ' ответ', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
