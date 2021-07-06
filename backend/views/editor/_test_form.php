<?php
/** @var $form yii\widgets\ActiveForm */
/** @var $model backend\models\editor\TestForm */
use backend\widgets\SelectTestWidget;
?>
<div class="row">
    <div class="col-xs-12">
        <?= $form->field($model, 'text', ['inputOptions' => ['class' => 'form-control input-sm']])->textInput() ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <?= $form->field($model, 'test_id', ['inputOptions' => ['class' => 'form-control input-sm']])->widget(SelectTestWidget::class) ?>
    </div>
</div>