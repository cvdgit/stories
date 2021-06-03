<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/** @var $model backend\models\editor\BaseForm */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Редактировать блок</h4>
</div>
<?php $form = ActiveForm::begin(['id' => 'block-form']); ?>
<div class="modal-body">
    <?= $this->render($model->view, ['form' => $form, 'model' => $model]) ?>
</div>
<div class="modal-footer">
    <?= $form->field($model, 'slide_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'block_id', ['inputOptions' => ['class' => 'editor-block-id']])->hiddenInput()->label(false) ?>
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'style' => 'margin-right: 20px']) ?>
</div>
<?php ActiveForm::end(); ?>
