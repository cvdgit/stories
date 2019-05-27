<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/** @var $model backend\models\SlideEditorForm */

$form = ActiveForm::begin([
    'action' => ['/editor/update-text'],
    'id' => 'block-form',
]);
echo $form->field($model, 'text_size')->textInput();
echo $form->field($model, 'text')->textArea(['rows' => 6]);
echo $form->field($model, 'story_id')->hiddenInput()->label(false);
echo $form->field($model, 'slide_index')->hiddenInput()->label(false);
echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
ActiveForm::end();
