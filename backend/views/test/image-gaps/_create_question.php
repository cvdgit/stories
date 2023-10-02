<?php

declare(strict_types=1);

use backend\Testing\Questions\ImageGaps\Create\CreateImageGapsForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var CreateImageGapsForm $formModel
 */

?>
<?php $form = ActiveForm::begin(['id' => 'image-gaps-form']); ?>
<?= $form->field($formModel, 'name')->textInput(['maxlength' => true]); ?>
<?= $form->field($formModel, 'image')->fileInput(); ?>
<?= $form->field($formModel, 'max_prev_items')->dropDownList($formModel->getMaxPrevItems())
    ->hint('При неправильном выборе возврат на указанное количество элементов'); ?>
<div>
    <?= Html::submitButton('Создать', ['class' => 'btn btn-primary']); ?>
</div>
<?php ActiveForm::end(); ?>
