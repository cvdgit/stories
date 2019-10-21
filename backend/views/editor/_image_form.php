<?php

use common\helpers\StoryHelper;
use yii\helpers\Html;

/** @var $model backend\models\editor\ImageForm */
$actionFieldID = Html::getInputId($model, 'action');
$actionStoryFieldID = Html::getInputId($model, 'actionStoryID');
$actionSlideFieldID = Html::getInputId($model, 'actionSlideID');
$js = <<< JS
function onChangeAction() {
    $("#$actionStoryFieldID").attr("disabled", !this.checked);
    $("#$actionSlideFieldID").attr("disabled", !this.checked);
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

echo $form->field($model, 'image')->fileInput();
echo $form->field($model, 'action')->checkbox();
echo $form->field($model, 'actionStoryID')->dropDownList(StoryHelper::getStoryArray(), ['onchange' => 'StoryEditor.changeStory(this, "imageform-actionslideid", ' . $model->actionSlideID . ')', 'prompt' => 'Выбрать историю']);
echo $form->field($model, 'actionSlideID')->dropDownList([], ['prompt' => 'Выбрать слайд']);
