<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/** @var $model backend\models\editor\ButtonForm */

$form = ActiveForm::begin([
    'action' => ['/editor/block/update', 'block_id' => $model->block_id],
    'id' => 'block-form',
]);
echo $form->field($model, 'text')->textInput();
echo $form->field($model, 'url')->textInput();
echo $form->field($model, 'slide_id')->hiddenInput()->label(false);
echo $form->field($model, 'block_id')->hiddenInput()->label(false);
echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'style' => 'margin-right: 20px']);
echo Html::a('Удалить блок', '#', ['class' => 'btn btn-danger', 'onclick' => "StoryEditor.deleteBlock('" . $model->block_id . "')"]);
ActiveForm::end();
