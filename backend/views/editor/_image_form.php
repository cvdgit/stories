<?php

use common\helpers\StoryHelper;
use yii\helpers\Html;

/** @var $model backend\models\editor\ImageForm */
$actionFieldID = Html::getInputId($model, 'action');
$actionStoryFieldID = Html::getInputId($model, 'actionStoryID');
$actionSlideFieldID = Html::getInputId($model, 'actionSlideID');
$actionBackToNextSlideID = Html::getInputId($model, 'back_to_next_slide');
$js = <<< JS
function onChangeAction() {
    $("#$actionStoryFieldID").attr("disabled", !this.checked);
    $("#$actionSlideFieldID").attr("disabled", !this.checked);
    $("#$actionBackToNextSlideID").attr("disabled", !this.checked);
}
$("#$actionFieldID")
    .on("change", onChangeAction)
    .change();
$("#$actionStoryFieldID").change();
JS;
/** @var $this yii\web\View */
$this->registerJs($js);

/** @var $form yii\widgets\ActiveForm */
$form->action = ['/editor/update-image'];

echo $form->field($model, 'image', ['inputOptions' => ['class' => 'form-control']])->fileInput();
echo $form->field($model, 'action', ['inputOptions' => ['class' => 'form-control input-sm']])->checkbox();
echo $form->field($model, 'actionStoryID', ['inputOptions' => ['class' => 'form-control input-sm']])->dropDownList(StoryHelper::getStoryArray(), ['onchange' => 'StoryEditor.changeStory(this, "imageform-actionslideid", ' . $model->actionSlideID . ')', 'prompt' => 'Выбрать историю']);
echo $form->field($model, 'actionSlideID', ['inputOptions' => ['class' => 'form-control input-sm']])->dropDownList([], ['prompt' => 'Выбрать слайд']);
echo $form->field($model, 'back_to_next_slide', ['inputOptions' => ['class' => 'form-control input-sm']])->checkbox();