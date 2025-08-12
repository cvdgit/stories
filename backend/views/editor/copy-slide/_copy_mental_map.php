<?php

declare(strict_types=1);

use backend\SlideEditor\CopyMentalMap\CopyForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var CopyForm $formModel
 */
?>
<?php $form = ActiveForm::begin([
    'id' => 'copy-course-form',
    'action' => ['/editor/copy-slide/mental-map']
]) ?>
<?= $form->field($formModel, 'name')->textInput() ?>
<div>
    <?= $form->field($formModel, 'id')->hiddenInput()->label(false) ?>
    <?= $form->field($formModel, 'slideId')->hiddenInput()->label(false) ?>
    <?= Html::submitButton('Скопировать', ['class' => 'btn btn-primary']) ?>
    <button class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
</div>
<?php ActiveForm::end() ?>
