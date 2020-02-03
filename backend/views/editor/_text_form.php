<?php

/** @var $form yii\widgets\ActiveForm */
$form->action = ['update-text'];
/** @var $model backend\models\editor\TextForm */
echo $form->field($model, 'text_size', ['inputOptions' => ['class' => 'form-control input-sm']])->textInput();
echo $form->field($model, 'text', ['inputOptions' => ['class' => 'form-control input-sm']])->textArea(['rows' => 4]);
