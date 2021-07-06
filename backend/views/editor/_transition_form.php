<?php
use backend\widgets\SelectStoryWidget;
use yii\helpers\Html;
/** @var $form yii\widgets\ActiveForm */
/** @var $model backend\models\editor\TransitionForm */
/** @var $widgetStoryModel common\models\StoryModel */
/** @var $this yii\web\View */
?>
<?= $form->field($model, 'text', ['inputOptions' => ['class' => 'form-control input-sm']])->textInput() ?>
<?= $form->field($model, 'transition_story_id', ['options' => ['class' => 'select-story-widget'], 'inputOptions' => ['class' => 'form-control input-sm']])
    ->widget(SelectStoryWidget::class, [
        'storyModel' => $widgetStoryModel,
        'linkedSlidesId' => Html::getInputId($model, 'slides'),
        'selectedSlideId' => $model->slides,
    ]) ?>
<?= $form->field($model, 'slides', ['inputOptions' => ['class' => 'form-control input-sm']])->dropDownList([], ['prompt' => 'Выбрать слайд']) ?>
<?= $form->field($model, 'back_to_next_slide', ['inputOptions' => ['class' => 'form-control input-sm']])->checkbox() ?>
