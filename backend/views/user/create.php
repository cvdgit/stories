<?php

/** @var $this yii\web\View */
/** @var $model backend\models\UserCreateForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Создание пользователя';
?>
<div class="user-create">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'username')->textInput(['maxLength' => true]) ?>
    <?= $form->field($model, 'email')->textInput(['maxLength' => true]) ?>
    <?= $form->field($model, 'password')->passwordInput(['maxLength' => true]) ?>
    <?= $form->field($model, 'role')->dropDownList($model->rolesList()) ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
