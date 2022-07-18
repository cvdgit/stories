<?php
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
/**
 * @var $this yii\web\View
 * @var $model modules\files\models\StudyFolder
 * @var $form yii\widgets\ActiveForm
 */
?>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'visible')->checkbox() ?>
<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? 'Создать папку' : 'Сохранить изменения', ['class' => 'btn btn-primary my-2']) ?>
</div>
<?php ActiveForm::end(); ?>
