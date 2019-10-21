<?php

/** @var $form yii\widgets\ActiveForm */
/** @var $model backend\models\editor\TransitionForm */

$form->action = ['/editor/update-transition'];
?>
<div class="row">
    <div class="col-xs-6"><?= $form->field($model, 'text')->textInput() ?></div>
    <div class="col-xs-6"><?= $form->field($model, 'text_size')->textInput() ?></div>
</div>
<div class="row">
    <div class="col-xs-6"><?= $form->field($model, 'transition_story_id')->dropDownList(common\helpers\StoryHelper::getStoryArray()) ?></div>
    <div class="col-xs-6"><?= $form->field($model, 'slides')->textInput()->hint('Пример: 1,2,3-10,12,15') ?></div>
</div>
<?php
