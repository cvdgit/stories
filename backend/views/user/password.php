<?php

/** @var $this yii\web\View */
/** @var $model backend\models\ChangePasswordForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Изменение пароля';
?>
<div class="user-create">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'password')->textInput(['maxLength' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>