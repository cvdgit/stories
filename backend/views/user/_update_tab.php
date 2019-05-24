<?php

use yii\widgets\ActiveForm;
use common\helpers\UserHelper;
use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $model common\models\User */

?>
<div class="row" style="margin-top: 20px">
    <div class="col-xs-6">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'status')->dropDownList(UserHelper::getStatusArray(), ['prompt' => 'Выбрать']) ?>
        <?= $form->field($model, 'role')->dropDownList($model->rolesList(), ['prompt' => 'Выбрать']) ?>
        <div class="form-group">
            <?= Html::submitButton('Сохранить изменения', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>