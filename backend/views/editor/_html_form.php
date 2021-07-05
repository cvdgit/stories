<?php
/** @var $form yii\widgets\ActiveForm */
/** @var $model backend\models\editor\QuestionForm */
use backend\widgets\SelectTestWidget;
?>
<?= $form->field($model, 'test_id', ['inputOptions' => ['class' => 'form-control input-sm']])->widget(SelectTestWidget::class) ?>
<?= $form->field($model, 'required', ['inputOptions' => ['class' => 'form-control input-sm']])->checkbox() ?>
