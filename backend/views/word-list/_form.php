<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\TestWordList */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="test-word-list-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-success']) ?>
        <?php if (!$model->isNewRecord): ?>
        <?= Html::a('Создать тест и историю', ['word-list/create-story-form', 'id' => $model->id], ['class' => 'btn', 'data-toggle' => 'modal', 'data-target' => '#create-test-and-story-modal']) ?>
        <?php endif ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<div class="modal remote fade" id="create-test-and-story-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
