<?php

declare(strict_types=1);

use modules\edu\forms\teacher\ParentInviteForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var ParentInviteForm $formModel
 */
?>

<div class="modal-header">
    <h5 class="modal-title">Пригласить родителя</h5>
    <button type="button" class="close" data-dismiss="modal">
        <span>&times;</span>
    </button>
</div>
<div class="modal-body">
    <div style="margin-top: 20px; margin-bottom: 20px">
        <?php $form = ActiveForm::begin(['id' => 'parent-invite-form', 'options' => ['class' => 'story-form']]) ?>
        <?= $form->field($formModel, 'email')->textInput(); ?>
        <?= Html::submitButton('Пригласить', ['class' => 'btn']) ?>
        <?php ActiveForm::end() ?>
    </div>
</div>
<div class="modal-footer">
    <div class="modal-footer-inner"></div>
</div>
