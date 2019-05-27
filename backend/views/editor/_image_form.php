<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/** @var $model backend\models\SlideEditorForm */

$form = ActiveForm::begin([
    'action' => ['/editor/update-image'],
    'options' => ['enctype' => 'multipart/form-data'],
    'id' => 'block-form',
]);
echo $form->field($model, 'image')->fileInput();
echo $form->field($model, 'story_id')->hiddenInput()->label(false);
echo $form->field($model, 'slide_index')->hiddenInput()->label(false);
echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
ActiveForm::end();