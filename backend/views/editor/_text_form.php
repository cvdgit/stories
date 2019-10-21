<?php

/** @var $form yii\widgets\ActiveForm */
$form->action = ['update-text'];
/** @var $model backend\models\editor\TextForm */
echo $form->field($model, 'text_size')->textInput();
echo $form->field($model, 'text')->textArea(['rows' => 4]);
