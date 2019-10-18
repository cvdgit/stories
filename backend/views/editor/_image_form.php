<?php

use common\helpers\StoryHelper;
use yii\widgets\ActiveForm;
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

$form = ActiveForm::begin([
    'action' => ['/editor/update-image'],
    'options' => ['enctype' => 'multipart/form-data'],
    'id' => 'block-form',
]);
?>
    <div class="row">
        <div class="col-xs-6"><?= $form->field($model, 'width') ?></div>
        <div class="col-xs-6"><?= $form->field($model, 'top') ?></div>
    </div>
    <div class="row">
        <div class="col-xs-6"><?= $form->field($model, 'height') ?></div>
        <div class="col-xs-6"><?= $form->field($model, 'left') ?></div>
    </div>
<?php
echo $form->field($model, 'image')->fileInput();
echo $form->field($model, 'action')->checkbox();
echo $form->field($model, 'actionStoryID')->dropDownList(StoryHelper::getStoryArray(), ['onchange' => 'StoryEditor.changeStory(this, "imageform-actionslideid", ' . $model->actionSlideID . ')', 'prompt' => 'Выбрать историю']);
echo $form->field($model, 'actionSlideID')->dropDownList([], ['prompt' => 'Выбрать слайд']);
echo $form->field($model, 'slide_id')->hiddenInput()->label(false);
echo $form->field($model, 'block_id')->hiddenInput()->label(false);
echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'style' => 'margin-right: 20px']);
echo Html::a('Удалить блок', '#', ['class' => 'btn btn-danger', 'onclick' => "StoryEditor.deleteBlock('" . $model->block_id . "')"]);
ActiveForm::end();