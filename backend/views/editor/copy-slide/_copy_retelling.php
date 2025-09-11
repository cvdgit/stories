<?php

declare(strict_types=1);

use backend\SlideEditor\CopyRetelling\CopyRetellingForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var CopyRetellingForm $formModel
 */
?>
<?php $form = ActiveForm::begin([
    'id' => 'copy-retelling-form',
    'action' => ['/editor/copy-slide/retelling']
]) ?>
<?= $form->field($formModel, 'name')->textInput() ?>
<div>
    <?= $form->field($formModel, 'id')->hiddenInput()->label(false) ?>
    <?= $form->field($formModel, 'slideId')->hiddenInput()->label(false) ?>
    <?= Html::submitButton('Скопировать', ['class' => 'btn btn-primary']) ?>
    <button class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
</div>
<?php ActiveForm::end() ?>
