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
        <div class="col-lg-8">
            <?php $form = ActiveForm::begin(['id' => 'create-answer-form']); ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($answerImageModel, 'answerImage')->fileInput() ?>
            <?php if ($model->haveImage()): ?>
            <div style="padding: 20px 0; text-align: center">
                <?= Html::img($model->getImagePath(), ['style' => 'max-width: 330px']) ?>
                <div>
                    <?= Html::a('Удалить изображение', ['answer/delete-image', 'id' => $model->id]) ?>
                </div>
            </div>
            <?php endif ?>
            <?= $form->field($model, 'is_correct')->checkbox() ?>
            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success', 'name' => 'action', 'value' => 'save']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

