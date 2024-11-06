<?php

declare(strict_types=1);

use backend\models\editor\MentalMapForm;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var ActiveForm $form
 * @var MentalMapForm $model
 * @var Bool $new
 */
?>
<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'mental_map_id')->hiddenInput()->label(false) ?>
<?php if ($new): ?>
<?= $form->field($model, 'use_slide_image', ['inputOptions' => ['class' => 'form-control input-sm']])->checkbox() ?>
<?php endif ?>
<?= $form->field($model, 'required', ['inputOptions' => ['class' => 'form-control input-sm']])->checkbox() ?>
