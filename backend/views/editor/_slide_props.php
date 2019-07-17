<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/** @var $model backend\models\editor\SlidePropsForm */

$form = ActiveForm::begin([
    'action' => ['/editor/update-slide'],
    'id' => 'block-form',
]);
echo $form->field($model, 'hidden')->checkbox();
echo $form->field($model, 'story_id')->hiddenInput()->label(false);
echo $form->field($model, 'slide_index')->hiddenInput()->label(false);
echo $form->field($model, 'slide_id')->hiddenInput()->label(false);
echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
ActiveForm::end();
