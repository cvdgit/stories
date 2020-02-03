<?php

use yii\helpers\Html;

/** @var $form yii\widgets\ActiveForm */
$form->action = ['/editor/update-transition'];

/** @var $model backend\models\editor\TransitionForm */
$actionStoryFieldID = Html::getInputId($model, 'transition_story_id');
$js = <<< JS
$("#$actionStoryFieldID").change();
JS;
/** @var $this yii\web\View */
$this->registerJs($js);
?>
<div class="row">
    <div class="col-xs-6"><?= $form->field($model, 'text', ['inputOptions' => ['class' => 'form-control input-sm']])->textInput() ?></div>
    <div class="col-xs-6"><?= $form->field($model, 'text_size', ['inputOptions' => ['class' => 'form-control input-sm']])->textInput() ?></div>
</div>
<div class="row">
    <div class="col-xs-6"><?= $form->field($model, 'transition_story_id', ['inputOptions' => ['class' => 'form-control input-sm']])->dropDownList(common\helpers\StoryHelper::getStoryArray(), ['onchange' => 'StoryEditor.changeStory(this, "transitionform-slides", ' . $model->slides . ')', 'prompt' => 'Выбрать историю']) ?></div>
    <div class="col-xs-6"><?= $form->field($model, 'slides', ['inputOptions' => ['class' => 'form-control input-sm']])->dropDownList([], ['prompt' => 'Выбрать слайд']) ?></div>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'back_to_next_slide', ['inputOptions' => ['class' => 'form-control input-sm']])->checkbox() ?>
    </div>
</div>
