<?php
use backend\widgets\SelectStoryWidget;
use yii\helpers\Html;
/** @var $model backend\models\editor\ImageForm */
/** @var $widgetStoryModel common\models\StoryModel */
/** @var $this yii\web\View */
/** @var $form yii\widgets\ActiveForm */
//echo $form->field($model, 'image', ['inputOptions' => ['class' => 'form-control']])->fileInput();
echo $form->field($model, 'action', ['inputOptions' => ['class' => 'form-control input-sm']])->checkbox();
echo $form->field($model, 'actionStoryID', ['options' => ['class' => 'select-story-widget'], 'inputOptions' => ['class' => 'form-control input-sm']])->widget(SelectStoryWidget::class, [
    'storyModel' => $widgetStoryModel,
    'linkedSlidesId' => Html::getInputId($model, 'actionSlideID'),
    'selectedSlideId' => $model->actionSlideID,
]);
echo $form->field($model, 'actionSlideID', ['inputOptions' => ['class' => 'form-control input-sm']])->dropDownList([], ['prompt' => 'Выбрать слайд']);
echo $form->field($model, 'back_to_next_slide', ['inputOptions' => ['class' => 'form-control input-sm']])->checkbox();
?>
<?= $form->field($model, 'description')->textarea() ?>
<?= $form->field($model, 'description_inside')->checkbox() ?>