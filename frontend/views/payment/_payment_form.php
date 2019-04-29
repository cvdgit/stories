<?php

/** @var $model frontend\models\PaymentForm */

use yii\bootstrap\ActiveForm;
use yii\web\JsExpression;

?>
<?php $form = ActiveForm::begin([
    'options' => [
        'name' => 'TinkoffPayForm',
        'onsubmit' => new JsExpression('pay(this); return false;'),
    ],
]); ?>
<?php $fieldOptions = ['template' => '{input}', 'options' => ['tag' => false]]; ?>
<?= $form->field($model, 'terminalkey', $fieldOptions)->hiddenInput()->label(false) ?>
<?= $form->field($model, 'frame', $fieldOptions)->hiddenInput()->label(false) ?>
<?= $form->field($model, 'language', $fieldOptions)->hiddenInput()->label(false) ?>
<?= $form->field($model, 'amount', $fieldOptions)->hiddenInput()->label(false) ?>
<?= $form->field($model, 'order', $fieldOptions)->hiddenInput()->label(false) ?>
<?= $form->field($model, 'description', $fieldOptions)->hiddenInput()->label(false) ?>
<?= $form->field($model, 'name', $fieldOptions)->hiddenInput()->label(false) ?>
<?= $form->field($model, 'email', $fieldOptions)->hiddenInput()->label(false) ?>
<?= $form->field($model, 'phone', $fieldOptions)->hiddenInput()->label(false) ?>
<?php ActiveForm::end(); ?>
