<?php
use backend\forms\CreateWordList;
use backend\widgets\SelectStoryWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model */
/* @var $form yii\widgets\ActiveForm */
$isNewRecord = $model instanceof CreateWordList;
?>
<div class="test-word-list-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'story')->widget(SelectStoryWidget::class) ?>
    <div class="form-group">
        <?= Html::submitButton($isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-success']) ?>
        <?php if (!$isNewRecord): ?>
        <?= Html::a('Создать тест и историю', ['word-list/create-story-form', 'id' => $model->id], ['class' => 'btn', 'data-toggle' => 'modal', 'data-target' => '#create-test-and-story-modal']) ?>
        <?php endif ?>
    </div>
    <?php ActiveForm::end(); ?>
    <?php if (!$isNewRecord): ?>
        <?= Html::a('Создать из шаблона', ['test-template/create-tests', 'word_list_id' => $model->id], ['class' => 'btn btn-default', 'data-toggle' => 'modal', 'data-target' => '#create-from-template-modal']) ?>
    <?php endif ?>
</div>

<div class="modal remote fade" id="create-test-and-story-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal remote fade" id="create-from-template-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>