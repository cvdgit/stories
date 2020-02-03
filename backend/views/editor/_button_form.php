<?php

/** @var $form yii\widgets\ActiveForm */
/** @var $model backend\models\editor\ButtonForm */
$form->action = ['/editor/update-button'];
?>
<div class="row">
    <div class="col-xs-6"><?= $form->field($model, 'text_size', ['inputOptions' => ['class' => 'form-control input-sm']])->textInput() ?></div>
    <div class="col-xs-6"></div>
</div>
<div class="row">
    <div class="col-xs-12"><?= $form->field($model, 'text', ['inputOptions' => ['class' => 'form-control input-sm']])->textInput() ?></div>
    <div class="col-xs-12"><?= $form->field($model, 'url', ['inputOptions' => ['class' => 'form-control input-sm']])->textInput() ?></div>
</div>
