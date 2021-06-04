<?php
/** @var $form yii\widgets\ActiveForm */
/** @var $model backend\models\editor\QuestionForm */
$form->action = ['editor/update-block/html'];
?>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'test_id', ['inputOptions' => ['class' => 'form-control input-sm']])->dropDownList(\common\models\StoryTest::getTestArray(), ['readonly' => true]) ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'required', ['inputOptions' => ['class' => 'form-control input-sm']])->checkbox() ?>
    </div>
</div>