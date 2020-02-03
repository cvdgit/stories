<?php

/** @var $form yii\widgets\ActiveForm */
/** @var $model backend\models\editor\TestForm */

$form->action = ['/editor/update-test'];
?>
<div class="row">
    <div class="col-xs-6"><?= $form->field($model, 'text', ['inputOptions' => ['class' => 'form-control input-sm']])->textInput() ?></div>
    <div class="col-xs-6"><?= $form->field($model, 'text_size', ['inputOptions' => ['class' => 'form-control input-sm']])->textInput() ?></div>
</div>
<div class="row">
    <div class="col-xs-6"><?= $form->field($model, 'test_id', ['inputOptions' => ['class' => 'form-control input-sm']])->dropDownList(\common\models\StoryTest::getTestArray()) ?></div>
</div>
