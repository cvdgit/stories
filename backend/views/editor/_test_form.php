<?php

/** @var $form yii\widgets\ActiveForm */
/** @var $model backend\models\editor\TestForm */

$form->action = ['/editor/update-test'];
?>
<div class="row">
    <div class="col-xs-6"><?= $form->field($model, 'text')->textInput() ?></div>
    <div class="col-xs-6"><?= $form->field($model, 'text_size')->textInput() ?></div>
</div>
<div class="row">
    <div class="col-xs-6"><?= $form->field($model, 'test_id')->dropDownList(\common\models\StoryTest::getTestArray()) ?></div>
</div>
