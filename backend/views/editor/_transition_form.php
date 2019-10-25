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
    <div class="col-xs-6"><?= $form->field($model, 'text')->textInput() ?></div>
    <div class="col-xs-6"><?= $form->field($model, 'text_size')->textInput() ?></div>
</div>
<div class="row">
    <div class="col-xs-6"><?= $form->field($model, 'transition_story_id')->dropDownList(common\helpers\StoryHelper::getStoryArray(), ['onchange' => 'StoryEditor.changeStory(this, "transitionform-slides", ' . $model->slides . ')', 'prompt' => 'Выбрать историю']) ?></div>
    <div class="col-xs-6"><?= $form->field($model, 'slides')->dropDownList([], ['prompt' => 'Выбрать слайд']) ?></div>
</div>
