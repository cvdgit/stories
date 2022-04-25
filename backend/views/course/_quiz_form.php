<?php
use backend\models\editor\QuestionForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use backend\widgets\SelectTestWidget;
/** @var array $action */
/** @var QuestionForm $model */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Редактировать блок</h4>
</div>
<?php $form = ActiveForm::begin(['id' => 'block-form', 'action' => $action]); ?>
<div class="modal-body">
    <?= $form->field($model, 'test_id', ['inputOptions' => ['class' => 'form-control input-sm']])->widget(SelectTestWidget::class) ?>
    <?= $form->field($model, 'required', ['inputOptions' => ['class' => 'form-control input-sm']])->checkbox() ?>
</div>
<div class="modal-footer">
    <?= $form->field($model, 'slide_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'story_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'lesson_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'block_id', ['inputOptions' => ['class' => 'editor-block-id']])->hiddenInput()->label(false) ?>
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
