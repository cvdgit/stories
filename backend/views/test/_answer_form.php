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
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'story_question_id')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($answerImageModel, 'answerImage')->fileInput() ?>
            <?php if (!$model->isNewRecord && $model->haveImage()): ?>
            <div style="padding: 20px 0; text-align: center">
                <?= Html::img($model->getImagePath(), ['style' => 'max-width: 330px']) ?>
                <div>
                    <?= Html::a('Удалить изображение', ['answer/delete-image', 'id' => $model->id]) ?>
                </div>
            </div>
            <?php endif ?>
            <?= $form->field($model, 'is_correct')->checkbox() ?>
            <div class="form-group">
                <?= Html::submitButton(($model->isNewRecord ? 'Создать ответ' : 'Сохранить'), ['class' => 'btn btn-success', 'name' => 'action', 'value' => 'save']) ?>
                <?php if ($model->isNewRecord): ?>
                    <?= Html::submitButton('Сохранить и создать новый ответ', ['class' => 'btn btn-link btn-sm', 'name' => 'action', 'value' => 'save-and-create']) ?>
                <?php endif ?>

                <?= Html::submitButton('Сохранить и вернуться к вопросу', ['class' => 'btn btn-link btn-sm', 'name' => 'action', 'value' => 'save-and-return']) ?>

            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
