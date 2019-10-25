<?php

/** @var $form yii\widgets\ActiveForm */
/** @var $model backend\models\editor\ButtonForm */
$form->action = ['/editor/block/update', 'block_id' => $model->block_id];
echo $form->field($model, 'text')->textInput();
echo $form->field($model, 'url')->textInput();
